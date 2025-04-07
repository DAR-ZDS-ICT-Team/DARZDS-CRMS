<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Section;
use App\Models\Services;
use Inertia\Inertia;
use App\Models\Assignatorees;
use App\Models\CSFForm;
use App\Models\Division;
use App\Models\Dimension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerAttributeRating;
use App\Models\CustomerComment;
use App\Models\CustomerCCRating;
use App\Http\Resources\Section as SectionResource;
use App\Http\Resources\Services as ServiceResource;
use App\Models\CustomerRecommendationRating;
use App\Http\Resources\Division as DivisionResource;
use App\Http\Resources\CustomerAttributeRatings as CARResource;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get assignatoree list and dimensions
        $assignatorees = Assignatorees::all();
        $dimensions = Dimension::all();

        // Fetch division (required)
        $division = Division::findOrFail($request->division_id);

        // Initialize variables
        $section = null;
        $service = null;
        $services = [];

        // If a section is selected
        if ($request->section_id) {
            // Get the section
            $sectionModel = Section::findOrFail($request->section_id);

            // Wrap it in a resource collection if needed
            $section = SectionResource::collection([$sectionModel]);

            // Get services under the section
            $services = Services::where('section_id', $sectionModel->id)->get();
            
            // Get specific service if provided
            if ($request->service_id) {
                $service = Services::findOrFail($request->service_id);
            }
        } else {
            // No section? Then get services directly under the division
            $services = Services::where('division_id', $division->id)->whereNull('section_id')->get();
            
            // Get specific service if provided
            if ($request->service_id) {
                $service = Services::findOrFail($request->service_id);
            }
        }

        return Inertia::render('CSI/Index', [
            'assignatorees' => $assignatorees,
            'dimensions'    => $dimensions,
            'division'      => $division,
            'section'       => $section,
            'service'       => $service,
            'services'      => $services,
            'user'          => $user,
        ]);
    }

    public function view(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Fetch dimensions and division
        $dimensions = Dimension::all();
        $division = Division::findOrFail($request->division_id);

        $section = null;
        $service = null;
        $services = [];

        // Check if section is selected
        if ($request->section_id) {
            $sectionModel = Section::findOrFail($request->section_id);
            $section = SectionResource::collection([$sectionModel]);

            // Fetch services under this section
            $services = Services::where('section_id', $sectionModel->id)->get();
            
            // Get specific service if provided
            if ($request->service_id) {
                $service = Services::findOrFail($request->service_id);
            }
        } else {
            // No section? Then fetch services directly under the division
            $services = Services::where('division_id', $division->id)->whereNull('section_id')->get();
            
            // Get specific service if provided
            if ($request->service_id) {
                $service = Services::findOrFail($request->service_id);
            }
        }

        return Inertia::render('Libraries/Division-Sections/Views/View', [
            'dimensions' => $dimensions,
            'division'   => $division,
            'section'    => $section,
            'service'    => $service,
            'services'   => $services,
            'user'       => $user,
        ]);
    }

    public function generateReports(Request $request)
    {    
        //get user
        $user = Auth::user();

        if($request->csi_type == 'By Date'){
            return $this->generateCSIBySectionByDate($request, $user->office_id);
        }
        else if($request->csi_type == "By Month"){
            return $this->generateCSIBySectionMonthly($request, $user->office_id);
        }
        else if($request->csi_type == "By Quarter"){
            return $this->generateCSIByQuarter($request, $user->office_id);
        }
        else if($request->csi_type == "By Year/Annual"){
            return $this->generateCSIBySectionYearly($request, $user->office_id);  
        }
    }

    // Method to get services from the request
    protected function getServices($request)
    {
        $services = [];
        
        if ($request->service_id) {
            // Return the specific service if service_id is provided
            $service = Services::find($request->service_id);
            if ($service) {
                $services = ServiceResource::collection([$service]);
            }
        } else if ($request->section_id) {
            // Return all services for a section
            $services = ServiceResource::collection(
                Services::where('section_id', $request->section_id)->get()
            );
        } else if ($request->division_id) {
            // Return all direct services for a division (not associated with any section)
            $services = ServiceResource::collection(
                Services::where('division_id', $request->division_id)
                    ->whereNull('section_id')
                    ->get()
            );
        }
        
        return $services;
    }

    // Get a service by ID
    protected function getServiceById($serviceId)
    {
        if (!$serviceId) {
            return null;
        }
        
        $service = Services::find($serviceId);
        return $service ? new ServiceResource($service) : null;
    }

    // Modified query method to search CSF forms with the new structure
    protected function querySearchCSF($office_id, $division_id, $section_id = null, $service_id = null, $client_type = null)
    {
        $query = CSFForm::when($office_id, function ($query, $office_id) {
            $query->where('office_id', $office_id);
        })
        ->when($division_id, function ($query, $division_id) {
            $query->where('division_id', $division_id);
        })
        ->when($section_id, function ($query, $section_id) {
            $query->where('section_id', $section_id);
        })
        ->when($service_id, function ($query, $service_id) {
            $query->where('service_id', $service_id);
        })
        ->when($client_type, function ($query, $client_type) use ($office_id, $division_id, $section_id) {
            // IF HR Section
            if($office_id == 10 && $division_id == 2 && $section_id == 8){
                if($client_type == "Internal"){
                    $query->where('client_type', "Internal Employees");
                }
                else if($client_type == "External"){
                    $query->where(function ($q) {
                        $q->where('client_type', "General Public")
                          ->orWhere('client_type', "Government Employees")
                          ->orWhere('client_type', "Business/Organization");
                    });
                }
            }
        });

        return $query->pluck('customer_id');
    }

    public function calculateCC($cc_query)
    {  
        // Clone the original query builder instance
        $cc_query_clone = clone $cc_query;

        // CC 1 
        $cc_query = clone $cc_query_clone;
        $cc1_ans4 = $cc_query->where('cc_id', 1)->where('answer', 4)->count();
        $cc_query = clone $cc_query_clone;
        $cc1_ans3 = $cc_query->where('cc_id', 1)->where('answer', 3)->count();
        $cc_query = clone $cc_query_clone;
        $cc1_ans2 = $cc_query->where('cc_id', 1)->where('answer', 2)->count();
        $cc_query = clone $cc_query_clone;
        $cc1_ans1 = $cc_query->where('cc_id', 1)->where('answer', 1)->count();

        // CC 2 
        $cc_query = clone $cc_query_clone;
        $cc2_ans5 = $cc_query->where('cc_id', 2)->where('answer', 5)->count();
        $cc_query = clone $cc_query_clone;
        $cc2_ans4 = $cc_query->where('cc_id', 2)->where('answer', 4)->count();
        $cc_query = clone $cc_query_clone;
        $cc2_ans3 = $cc_query->where('cc_id', 2)->where('answer', 3)->count();
        $cc_query = clone $cc_query_clone;
        $cc2_ans2 = $cc_query->where('cc_id', 2)->where('answer', 2)->count();
        $cc_query = clone $cc_query_clone;
        $cc2_ans1 = $cc_query->where('cc_id', 2)->where('answer', 1)->count();

        // CC 3
        $cc_query = clone $cc_query_clone;
        $cc3_ans4 = $cc_query->where('cc_id', 3)->where('answer', 4)->count();
        $cc_query = clone $cc_query_clone;
        $cc3_ans3 = $cc_query->where('cc_id', 3)->where('answer', 3)->count();
        $cc_query = clone $cc_query_clone;
        $cc3_ans2 = $cc_query->where('cc_id', 3)->where('answer', 2)->count();
        $cc_query = clone $cc_query_clone;
        $cc3_ans1 = $cc_query->where('cc_id', 3)->where('answer', 1)->count();

        // cc 1-3 data
        $cc1_data = [
            'cc1_ans4' => $cc1_ans4,
            'cc1_ans3' => $cc1_ans3,
            'cc1_ans2' => $cc1_ans2,
            'cc1_ans1' => $cc1_ans1,
        ];

        $cc2_data = [
            'cc2_ans5' => $cc2_ans5,
            'cc2_ans4' => $cc2_ans4,
            'cc2_ans3' => $cc2_ans3,
            'cc2_ans2' => $cc2_ans2,
            'cc2_ans1' => $cc2_ans1,
        ];

        $cc3_data = [
            'cc3_ans4' => $cc3_ans4,
            'cc3_ans3' => $cc3_ans3,
            'cc3_ans2' => $cc3_ans2,
            'cc3_ans1' => $cc3_ans1,
        ];

        //cc data all in one
        $cc_data = [
            'cc1_data' => $cc1_data,
            'cc2_data' => $cc2_data,
            'cc3_data' => $cc3_data,
        ];

        return $cc_data;
    }

    public function generateCSIBySectionByDate($request, $office_id)
    {
        // Get services
        $services = $this->getServices($request);

        //get user
        $user = Auth::user();
        //get assignatoree list
        $assignatorees = Assignatorees::all();
        
        $division_id = $request->division['id'];
        // Handle both section and direct service scenarios
        $section_id = $request->section_id ?? null;
        $service_id = $request->service_id ?? null;
        $client_type = $request->client_type; 

        // Modify query to handle both section->service and division->service paths
        $customer_ids = $this->querySearchCSF($office_id, $division_id, $section_id, $service_id, $client_type);

        $cc_query = CustomerCCRating::whereBetween('created_at', [$request->date_from, $request->date_to])
            ->whereIn('customer_id', $customer_ids)
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            });

        //calculate CC
        $cc_data = $this->calculateCC($cc_query);
        $date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
            ->whereBetween('created_at', [$request->date_from, $request->date_to])
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            })
            ->get();

        $customer_recommendation_ratings = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
            ->whereBetween('created_at', [$request->date_from, $request->date_to])
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            })
            ->get();   
                    
        $dimensions = Dimension::all();
        $dimension_count = $dimensions->count();

        // total number of respondents/customer
        $total_respondents = $date_range->groupBy('customer_id')->count();

        // total number of respondents/customer who rated VS/S
        $total_vss_respondents = $date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();
        
        // total number of promoters or respondents who rated 9-10 in recommendation rating
        $total_promoters = $customer_recommendation_ratings->where('recommend_rate_score', '>', '8')->groupBy('customer_id')->count();
        
        // total number of detractors or respondents who rated 0-6 in recommendation rating
        $total_detractors = $customer_recommendation_ratings->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();

        $ilsr_grand_total = 0;
        // loop for getting importance ls rating grand total for ws rating calculation
        for ($dimensionId = 1; $dimensionId <= $dimension_count; $dimensionId++) {
            $vi_total = $date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->count();
            $i_total = $date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->count();
            $mi_total = $date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->count();
            $li_total = $date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->count();
            $nai_total = $date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->count();

            $x_vi_total = $vi_total * 5; 
            $x_i_total = $i_total * 4; 
            $x_mi_total = $mi_total * 3; 
            $x_li_total = $li_total * 2; 
            $x_nai_total = $nai_total * 1;
            $x_importance_total = $x_vi_total + $x_i_total + $x_mi_total + $x_li_total + $x_nai_total; 

            // Importance Likert Scale Rating 
            if($x_importance_total != 0 && $total_respondents != 0){
                $ilsr_total = $x_importance_total / $total_respondents;
                $ilsr_grand_total = $ilsr_grand_total + $ilsr_total;
            }
        }
        // PART I : CUSTOMER RATING OF SERVICE QUALITY 
        //set initial value of buttom side total scores
        $y_totals = [];
        $grand_vs_total = 0;
        $grand_s_total = 0;
        $grand_n_total = 0;
        $grand_vd_total = 0;
        $grand_d_total = 0;
        $grand_total = 0;
        
        //set initial value of right side total scores
        $x_vs_total = 0; 
        $x_s_total = 0; 
        $x_n_total = 0; 
        $x_d_total = 0; 
        $x_vd_total = 0; 
        $x_grand_total = 0; 

        $likert_scale_rating_totals = [];
        $lsr_total = 0;
        $lsr_grand_total = 0;

        // PART II : IMPORTANCE OF THIS ATTRIBUTE 
        //set importance rating score 
        $importance_rate_score_totals = [];
        $x_importance_totals = [];
        $x_importance_total = 0; 

        $x_vi_total = 0; 
        $x_i_total = 0; 
        $x_mi_total = 0; 
        $x_li_total = 0; 
        $x_nai_total = 0;

        $importance_ilsr_totals = [];
        $ilsr_total = 0;

        $gap_totals = [];
        $gap_total = 0;
        $gap_grand_total = 0;
        $ss_total = 0;
        $ss_totals = [];
        $wf_total = 0;
        $wf_totals = [];
        $ws_total = 0;
        $ws_totals = [];
        $ws_grand_total = 0;

        for ($dimensionId = 1; $dimensionId <= $dimension_count; $dimensionId++) {
            //PART II
            $vs_total = $date_range->where('rate_score', 5)->where('dimension_id', $dimensionId)->count();
            $s_total = $date_range->where('rate_score', 4)->where('dimension_id', $dimensionId)->count();
            $n_total = $date_range->where('rate_score', 3)->where('dimension_id', $dimensionId)->count();
            $d_total = $date_range->where('rate_score', 2)->where('dimension_id', $dimensionId)->count();
            $vd_total = $date_range->where('rate_score', 1)->where('dimension_id', $dimensionId)->count();          
    
            $x_vs_total = $vs_total * 5; 
            $x_s_total = $s_total * 4; 
            $x_n_total = $n_total * 3; 
            $x_d_total = $d_total * 2; 
            $x_vd_total = $vd_total * 1; 

            // sum of all repondent with rate_score 1-5
            $x_respondents_total = $vs_total + $s_total + $n_total + $d_total + $vd_total;
            $x_grand_total = $x_vs_total + $x_s_total + $x_n_total + $x_d_total + $x_vd_total; 
        
            // right side total score divided by total repondents or customers
            if($x_grand_total != 0){
                if($dimensionId == 6){
                    $lsr_total = $x_grand_total / $x_respondents_total;
                }
                else{
                    $lsr_total = $total_respondents > 0 ? $x_grand_total / $total_respondents : 0;
                }
            }
        
            // SS = lsr with 3 decimals
            $ss_total = number_format($lsr_total, 3);
            $ss_totals[$dimensionId] = [
                'ss_total' => $ss_total,
            ];

            //likert scale rating grandtotal
            $lsr_grand_total = $lsr_grand_total + $lsr_total;
            $x_totals[$dimensionId] = [
                'x_total_score' => $x_grand_total,
            ];

            $lsr_total = number_format($lsr_total, 2);

            $likert_scale_rating_totals[$dimensionId] = [
                'lsr_total' => $lsr_total,
            ];

            $y_totals[$dimensionId] = [
                'vs_total' => $vs_total,
                's_total' => $s_total,
                'n_total' => $n_total,
                'd_total' => $d_total,
                'vd_total' => $vd_total,
            ];

            $grand_vs_total += $vs_total;
            $grand_s_total += $s_total;
            $grand_n_total += $n_total;
            $grand_d_total += $d_total;
            $grand_vd_total += $vd_total;

            // PART III
            $vi_total = $date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->count();
            $i_total = $date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->count();
            $mi_total = $date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->count();
            $li_total = $date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->count();
            $nai_total = $date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->count();
        
            $importance_rate_score_totals[$dimensionId] = [
                'vi_total' => $vi_total,
                'i_total' => $i_total,
                'mi_total' => $mi_total,
                'li_total' => $li_total,
                'nai_total' => $nai_total,
            ];

            $x_vi_total = $vi_total * 5; 
            $x_i_total = $i_total * 4; 
            $x_mi_total = $mi_total * 3; 
            $x_li_total = $li_total * 2; 
            $x_nai_total = $nai_total * 1;
            $x_importance_total = $x_vi_total + $x_i_total + $x_mi_total + $x_li_total + $x_nai_total; 
            
            //right side total importance rate scores 
            $x_importance_totals[$dimensionId] = [
                'x_importance_total_score' => $x_importance_total,
            ];
            
            // Likert Scale Rating 
            if($x_importance_total != 0 && $total_respondents > 0){
                $ilsr_total = $x_importance_total / $total_respondents;
            } else {
                $ilsr_total = 0;
            }
            $ilsr_total = number_format($ilsr_total, 2);

            $importance_ilsr_totals[$dimensionId] = [
                'ilsr_total' => $ilsr_total,
            ];

            // GAP = attributes total score minus importance of attributes total score
            $gap_total = $ilsr_total - $lsr_total;
            $gap_total = number_format($gap_total, 2);

            $gap_totals[$dimensionId] = [
                'gap_total' => $gap_total,
            ];

            $gap_grand_total += $gap_total;
            $gap_grand_total = number_format($gap_grand_total, 2);

            // WF = (importance LS Rating divided by importance grand total of ls rating) * 100
            if($ilsr_total != 0 && $ilsr_grand_total != 0){
                $wf_total = ($ilsr_total / $ilsr_grand_total) * 100;
            } else {
                $wf_total = 0;
            }
            $wf_total = number_format($wf_total, 2);
            $wf_totals[$dimensionId] = [
                'wf_total' => $wf_total,
            ];

            // WS = (SS * WF) / 100  
            $ws_total = ($ss_total * $wf_total) / 100;   

            $ws_total = number_format($ws_total, 2);
            $ws_grand_total += $ws_total;
            $ws_grand_total = number_format($ws_grand_total, 2);
            $ws_totals[$dimensionId] = [
                'ws_total' => $ws_total,
            ];
        }

        // round off Likert Scale Rating grand total and control decimal to 2 
        $lsr_grand_total = ($dimension_count > 0) ? ($lsr_grand_total / $dimension_count) : 0;
        $lsr_grand_total = number_format($lsr_grand_total, 2);    

        // table below total score
        $grand_vs_total = $grand_vs_total * 5;
        $grand_s_total = $grand_s_total * 4;
        $grand_n_total = $grand_n_total * 3;
        $grand_d_total = $grand_d_total * 2;
        $grand_vd_total = $grand_vd_total * 1;

        $x_grand_total = $grand_vs_total + $grand_s_total + $grand_n_total + $grand_d_total + $grand_vd_total;

        //Percentage of Respondents/Customers who rated VS/S: 
        // = total no. of respondents / total no. respondets who rated vs/s * 100
        $percentage_vss_respondents = 0;
        if($total_respondents != 0 && $total_vss_respondents != 0){
            $percentage_vss_respondents = ($total_vss_respondents / $total_respondents) * 100;
        }
        $percentage_vss_respondents = number_format($percentage_vss_respondents, 2);

        $customer_satisfaction_rating = 0;
        if($x_grand_total > 0){
            $customer_satisfaction_rating = (($grand_vs_total + $grand_s_total) / $x_grand_total) * 100;
        }
        $customer_satisfaction_rating = number_format($customer_satisfaction_rating, 2);

        // Customer Satisfaction Index (CSI) = (ws grand total / 5) * 100
        $customer_satisfaction_index = 0;
        if($ws_grand_total != 0){
            $customer_satisfaction_index = ($ws_grand_total / 5) * 100;
        }
        $customer_satisfaction_index = number_format($customer_satisfaction_index, 2);

        if($customer_satisfaction_index > 100){
            $customer_satisfaction_index = number_format(100, 2);
        }

        //Percentage of Promoters = number of promoters / total respondents
        $percentage_promoters = 0;
        if($total_respondents != 0 && $total_promoters > 0){
            $percentage_promoters = number_format((($total_promoters / $total_respondents) * 100), 2);
        }

        //Percentage of Detractors = number of detractors / total respondents
        $percentage_detractors = 0;
        if($total_respondents != 0 && $total_detractors > 0){
            $percentage_detractors = number_format((($total_detractors / $total_respondents) * 100), 2);
        }

        // Net Promotion Scores(NPS) = Percentage of Promoters−Percentage of Detractors
        $net_promoter_score = number_format(($percentage_promoters - $percentage_detractors), 2);

        // Get section data if it exists
        $section = null;
        if ($section_id) {
            $section = $request->section;
        }
        
        // Get service data
        $service = $this->getServiceById($service_id);

        //send response to front end
        return Inertia::render('CSI/Index')    
            ->with('user', $user)
            ->with('assignatorees', $assignatorees)
            ->with('cc_data', $cc_data) 
            ->with('services', $services)
            ->with('dimensions', $dimensions)
            ->with('division', $request->division)
            ->with('section', $section)
            ->with('service', $service)
            ->with('y_totals', $y_totals)
            ->with('grand_vs_total', $grand_vs_total)
            ->with('grand_s_total', $grand_s_total)
            ->with('grand_n_total', $grand_n_total)
            ->with('grand_d_total', $grand_d_total)
            ->with('grand_vd_total', $grand_vd_total)
            ->with('x_totals', $x_totals)
            ->with('x_grand_total', $x_grand_total)
            ->with('likert_scale_rating_totals', $likert_scale_rating_totals)
            ->with('lsr_grand_total', $lsr_grand_total)
            ->with('importance_rate_score_totals', $importance_rate_score_totals)
            ->with('x_importance_totals', $x_importance_totals)
            ->with('importance_ilsr_totals', $importance_ilsr_totals)
            ->with('gap_totals', $gap_totals)
            ->with('gap_grand_total', $gap_grand_total)
            ->with('wf_totals', $wf_totals)
            ->with('ss_totals', $ss_totals)
            ->with('ws_totals', $ws_totals)
            ->with('total_respondents', $total_respondents)
            ->with('total_vss_respondents', $total_vss_respondents)
            ->with('percentage_vss_respondents', $percentage_vss_respondents)
            ->with('customer_satisfaction_rating', $customer_satisfaction_rating)
            ->with('customer_satisfaction_index', $customer_satisfaction_index)
            ->with('net_promoter_score', $net_promoter_score)
            ->with('percentage_promoters', $percentage_promoters)
            ->with('percentage_detractors', $percentage_detractors)
            ->with('request', $request);
    }

    public function getCitizenCharterByQuarter($request, $customer_ids, $startDate, $endDate)
    {
        $cc_query = CustomerCCRating::whereBetween('created_at', [$startDate, $endDate])
            ->whereYear('created_at', $request->selected_year)
            ->whereIn('customer_id', $customer_ids)
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            });
        return $cc_query;
    }

    public function getCustomerAttributeRatingByQuarter($request, $customer_ids, $startDate, $endDate)
    {
        $query = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereYear('created_at', $request->selected_year)
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            })->get(); 

        return $query;
    }

    public function getCustomerAttributeRatingByQuarterWithMonth($request, $customer_ids, $numeric_month)
    {
        $query = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
            ->whereMonth('created_at', $numeric_month)
            ->whereYear('created_at', $request->selected_year)
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            })->get(); 

        return $query;
    }

    public function getCustomerRecommendationRatingByQuarter($request, $customer_ids, $startDate, $endDate)
    {
        $query = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereYear('created_at', $request->selected_year)
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            })->get(); 

        return $query;
    }

    public function getCustomerRecommendationRatingByQuarterWithMonth($request, $customer_ids, $numeric_month)
    {
        $query = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
            ->whereMonth('created_at', $numeric_month)
            ->whereYear('created_at', $request->selected_year)
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            })->get();

        return $query;
    }

    public function getRespondents($request, $customer_ids, $startDate, $endDate)
    {
        $respondents_list = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereYear('created_at', $request->selected_year)
            ->when($request->sex, function ($query, $sex) {
                $query->whereHas('customer', function ($query) use ($sex) {
                    $query->where('sex', $sex);
                });
            })
            ->when($request->age_group, function ($query, $age_group) {
                $query->whereHas('customer', function ($query) use ($age_group) {
                    $query->where('age_group', $age_group);
                });
            })->get();

        return $respondents_list;
    }

    public function generateCSIBySectionMonthly($request, $office_id)
    {
        // Get services
        $services = $this->getServices($request);
    
        //get user
        $user = Auth::user();
        //get assignatoree list
        $assignatorees = Assignatorees::all();
    
        $date_range = null;
        $customer_recommendation_ratings = null;
        $respondents_list = null;
    
        $division_id = $request->division['id'];
        $section_id = $request->section_id ?? null;
        $service_id = $request->service_id ?? null;
        $client_type = $request->client_type; 
    
        // Query for customer IDs based on the new structure
        $customer_ids = $this->querySearchCSF($office_id, $division_id, $section_id, $service_id, $client_type);
            
        $numericMonth = Carbon::parse("1 {$request->selected_month}")->format('m');
    
        $cc_query = CustomerCCRating::whereMonth('created_at', $numericMonth)
                                    ->whereYear('created_at', $request->selected_year)
                                    ->whereIn('customer_id',$customer_ids)
                                    ->when($request->sex, function ($query, $sex) {
                                        $query->whereHas('customer', function ($query) use ($sex) {
                                            $query->where('sex', $sex);
                                        });
                                    })
                                    ->when($request->age_group, function ($query, $age_group) {
                                        $query->whereHas('customer', function ($query) use ($age_group) {
                                            $query->where('age_group', $age_group);
                                        });
                                    });
    
        //calculate Citizen's Charter
        $cc_data = $this->calculateCC($cc_query);
    
        $date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                                                ->whereMonth('created_at', $numericMonth)
                                                ->whereYear('created_at', $request->selected_year)
                                                ->when($request->sex, function ($query, $sex) {
                                                $query->whereHas('customer', function ($query) use ($sex) {
                                                    $query->where('sex', $sex);
                                                });
                                            })
                                            ->when($request->age_group, function ($query, $age_group) {
                                                $query->whereHas('customer', function ($query) use ($age_group) {
                                                    $query->where('age_group', $age_group);
                                                });
                                            })
                                            ->get();
    
        $customer_recommendation_ratings = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
                                            ->whereMonth('created_at', $numericMonth)
                                            ->whereYear('created_at', $request->selected_year)
                                            ->when($request->sex, function ($query, $sex) {
                                                $query->whereHas('customer', function ($query) use ($sex) {
                                                    $query->where('sex', $sex);
                                                });
                                            })
                                            ->when($request->age_group, function ($query, $age_group) {
                                                $query->whereHas('customer', function ($query) use ($age_group) {
                                                    $query->where('age_group', $age_group);
                                                });
                                            })
                                            ->get();
        // List of Respondents/Customers
        $respondents_list = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                                                    ->whereMonth('created_at', $numericMonth)
                                                    ->whereYear('created_at', $request->selected_year)
                                                    ->get();
            
        // Dimensions or attributes
        $dimensions = Dimension::all();
        $dimension_count = $dimensions->count();
    
        // total number of respondents/customer
        $total_respondents = $date_range->groupBy('customer_id')->count();
    
        // total number of respondents/customer who rated VS/S
        $total_vss_respondents = $date_range->where('rate_score', '>','3')->groupBy('customer_id')->count();
        
        // total number of promoters or respondents who rated 7-10 in recommendation rating
        $total_promoters = $customer_recommendation_ratings->where('recommend_rate_score', '>','6')->groupBy('customer_id')->count();
        
        // total number of detractors or respondents who rated 0-6 in recommendation rating
        $total_detractors = $customer_recommendation_ratings->where('recommend_rate_score', '<','7')->groupBy('customer_id')->count();
    
        $ilsr_grand_total = 0;
        // loop for getting importance ls rating grand total for ws rating calculation
        for ($dimensionId = 1; $dimensionId <= $dimension_count; $dimensionId++) {
            $vi_total = $date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->count();
            $i_total = $date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->count();
            $mi_total = $date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->count();
            $li_total = $date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->count();
            $nai_total = $date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->count();
    
            $x_vi_total = $vi_total * 5; 
            $x_i_total = $i_total * 4; 
            $x_mi_total = $mi_total * 3; 
            $x_li_total = $li_total * 2; 
            $x_nai_total = $nai_total * 1;
            $x_importance_total = $x_vi_total + $x_i_total + $x_mi_total + $x_li_total + $x_nai_total; 
    
            // Importance Likert Scale RAting 
            if ($total_respondents > 0 && $x_importance_total != 0) {
                $ilsr_total = $x_importance_total / $total_respondents;
                $ilsr_grand_total = $ilsr_grand_total + $ilsr_total;
            }
        }
        
        // PART II : CUSTOMER RATING OF SERVICE QUALITY 
    
        //set initial value of buttom side total scores
        $y_totals = [];
        $grand_na_total = 0;
        $grand_vs_total = 0;
        $grand_s_total = 0;
        $grand_n_total = 0;
        $grand_vd_total = 0;
        $grand_d_total = 0;
        $grand_total = 0;
        
        //set initial value of right side total scores
        $x_vs_total = 0; 
        $x_s_total = 0; 
        $x_n_total = 0; 
        $x_d_total = 0; 
        $x_vd_total = 0; 
        $x_grand_total = 0; 
    
        $likert_scale_rating_totals = [];
        $lsr_total = 0;
        $lsr_grand_total = 0;
    
        // PART II : IMPORTANCE OF THIS ATTRIBUTE 
    
        //set importance rating score 
        $importance_rate_score_totals = [];
        $x_importance_totals = [];
        $x_importance_total = 0; 
    
        $x_vi_total = 0; 
        $x_i_total = 0; 
        $x_mi_total = 0; 
        $x_li_total = 0; 
        $x_nai_total = 0;
    
        $importance_ilsr_totals = [];
        $ilsr_total = 0;
    
        $gap_totals = [];
        $gap_total = 0;
        $gap_grand_total = 0;
        $ss_total = 0;
        $ss_totals = [];
        $wf_total = 0;
        $wf_totals = [];
        $ws_total = 0;
        $ws_totals = [];
        $ws_grand_total = 0;

        for ($dimensionId = 1; $dimensionId <= $dimension_count; $dimensionId++) {
            //PART I :
            $na_total = $date_range->where('rate_score', 6)->where('dimension_id', $dimensionId)->count(); 
    
            $vs_total = $date_range->where('rate_score', 5)->where('dimension_id', $dimensionId)->count();
            $s_total = $date_range->where('rate_score', 4)->where('dimension_id', $dimensionId)->count();
            $n_total = $date_range->where('rate_score', 3)->where('dimension_id', $dimensionId)->count();
            $d_total = $date_range->where('rate_score', 2)->where('dimension_id', $dimensionId)->count();
            $vd_total = $date_range->where('rate_score', 1)->where('dimension_id', $dimensionId)->count(); 
    
            // calculation for total score per dimension
            $x_vs_total = $vs_total * 5; 
            $x_s_total = $s_total * 4; 
            $x_n_total = $n_total * 3; 
            $x_d_total = $d_total * 2; 
            $x_vd_total = $vd_total * 1; 
    
            // sum of all repondent with rate_score 1-5
            $x_respondents_total = $vs_total + $s_total + $n_total + $d_total + $vd_total;
            $x_grand_total = $x_vs_total + $x_s_total + $x_n_total + $x_d_total + $x_vd_total; 
    
            // right side total score divided by total repondents or customers
            if ($x_grand_total != 0) {
                if ($dimensionId == 6) {
                    $lsr_total = $x_respondents_total > 0 ? $x_grand_total / $x_respondents_total : 0;
                } else {
                    $lsr_total = $total_respondents > 0 ? $x_grand_total / $total_respondents : 0;
                }
            } else {
                $lsr_total = 0;
            }
            
            // SS = lsr with 3 decimals
            $ss_total = number_format($lsr_total, 3);
            $ss_totals[$dimensionId] = [
                'ss_total' => $ss_total,
            ];
    
            //likert scale rating grandtotal
            $lsr_grand_total = $lsr_grand_total + $lsr_total;
            $x_totals[$dimensionId] = [
                'x_total_score' => $x_grand_total,
            ];
    
            $lsr_total = number_format($lsr_total, 2);
    
            $likert_scale_rating_totals[$dimensionId] = [
                'lsr_total' => $lsr_total,
            ];
    
            $y_totals[$dimensionId] = [
                'vs_total' => $vs_total,
                's_total' => $s_total,
                'n_total' => $n_total,
                'd_total' => $d_total,
                'vd_total' => $vd_total,
            ];
            
            $grand_na_total += $na_total;  
    
            $grand_vs_total += $vs_total;
            $grand_s_total += $s_total;
            $grand_n_total += $n_total;
            $grand_d_total += $d_total;
            $grand_vd_total += $vd_total;       
                        
            // PART II :
            $vi_total = $date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->count();
            $i_total = $date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->count();
            $mi_total = $date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->count();
            $li_total = $date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->count();
            $nai_total = $date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->count();
        
            $importance_rate_score_totals[$dimensionId] = [
                'vi_total' => $vi_total,
                'i_total' => $i_total,
                'mi_total' => $mi_total,
                'li_total' => $li_total,
                'nai_total' => $nai_total,
            ];
    
            $x_vi_total = $vi_total * 5; 
            $x_i_total = $i_total * 4; 
            $x_mi_total = $mi_total * 3; 
            $x_li_total = $li_total * 2; 
            $x_nai_total = $nai_total * 1;
            $x_importance_total = $x_vi_total + $x_i_total + $x_mi_total + $x_li_total + $x_nai_total; 
            
            //right side total importance rate scores 
            $x_importance_totals[$dimensionId] = [
                'x_importance_total_score' => $x_importance_total,
            ];
            
            // Likert Scale Rating 
            if ($total_respondents > 0 && $x_importance_total != 0) {
                $ilsr_total = $x_importance_total / $total_respondents;
            } else {
                $ilsr_total = 0;
            }
            $ilsr_total = number_format($ilsr_total, 2);
    
            $importance_ilsr_totals[$dimensionId] = [
                'ilsr_total' => $ilsr_total,
            ];
    
            // GAP = attributes total score minus importance of attributes total score
            $gap_total = $ilsr_total - $lsr_total;
            $gap_total = number_format($gap_total, 2);
    
            $gap_totals[$dimensionId] = [
                'gap_total' => $gap_total,
            ];
    
            $gap_grand_total += $gap_total;
            $gap_grand_total = number_format($gap_grand_total, 2);
    
            // WF = (importance LS Rating divided by importance grand total of ls rating) * 100
            if ($ilsr_grand_total > 0 && $ilsr_total != 0) {
                $wf_total = ($ilsr_total / $ilsr_grand_total) * 100;
            } else {
                $wf_total = 0;
            }
            $wf_total = number_format($wf_total, 2);
            $wf_totals[$dimensionId] = [
                'wf_total' => $wf_total,
            ];
    
            // WS = (SS * WF) / 100  
            $ws_total = ($ss_total * $wf_total) / 100;   
            $ws_grand_total = $ws_grand_total + $ws_total;
            $ws_total = number_format($ws_total, 2);
            $ws_grand_total = number_format($ws_grand_total, 2);
            $ws_totals[$dimensionId] = [
                'ws_total' => $ws_total,
            ];
        }
    
        //Calculate total number of respondents/customer who rated VS/S
        $vss_total = $grand_vs_total + $grand_s_total + $grand_na_total;
        $total_vss_respondents = $dimension_count > 0 ? $vss_total / $dimension_count : 0;     
        $total_vss_respondents = round($total_vss_respondents);      
    
        // round off Likert Scale Rating grand total and control decimal to 2 
        $lsr_grand_total = $dimension_count > 0 ? ($lsr_grand_total / $dimension_count) : 0;
        $lsr_grand_total = number_format($lsr_grand_total, 2);      
        
        // table below TOTAL SCORES
        $grand_vs_total = $grand_vs_total * 5;
        $grand_s_total = $grand_s_total * 4;
        $grand_n_total = $grand_n_total * 3;
        $grand_d_total = $grand_d_total * 2;
        $grand_vd_total = $grand_vd_total * 1;
    
        $x_grand_total = $grand_vs_total + $grand_s_total + $grand_n_total + $grand_d_total + $grand_vd_total;
    
        //Percentage of Respondents/Customers who rated VS/S: 
        $percentage_vss_respondents = 0;
        if ($total_respondents > 0) {
            $percentage_vss_respondents = ($total_vss_respondents / $total_respondents) * 100;
        }
        $percentage_vss_respondents = number_format($percentage_vss_respondents, 2);
    
        $customer_satisfaction_rating = 0;
        if ($x_grand_total > 0) {
            $customer_satisfaction_rating = (($grand_vs_total + $grand_s_total) / $x_grand_total) * 100;
        }
        $customer_satisfaction_rating = number_format($customer_satisfaction_rating, 2);
    
        // Customer Satisfaction Index (CSI) = (ws grand total / 5) * 100
        $customer_satisfaction_index = 0;
        if ($ws_grand_total > 0) {
            $customer_satisfaction_index = ($ws_grand_total / 5) * 100;
        }
        $customer_satisfaction_index = number_format($customer_satisfaction_index, 2);
    
        if ($customer_satisfaction_index > 100) {
            $customer_satisfaction_index = number_format(100, 2);
        }
    
        //Percentage of Promoters = number of promoters / total respondents
        $percentage_promoters = 0;
        if ($total_respondents > 0 && $total_promoters > 0) {
            $percentage_promoters = number_format((($total_promoters / $total_respondents) * 100), 2);
        }
    
        //Percentage of Detractors = number of detractors / total respondents
        $percentage_detractors = 0;
        if ($total_respondents > 0 && $total_detractors > 0) {
            $percentage_detractors = number_format((($total_detractors / $total_respondents) * 100), 2);
        }
    
        // Net Promotion Scores(NPS) = Percentage of Promoters−Percentage of Detractors
        $net_promoter_score = number_format(($percentage_promoters - $percentage_detractors), 2);
        
        //Respondents list
        $data = CARResource::collection($respondents_list);
    
        //comments and complaints
        $comment_list = CustomerComment::whereIn('customer_id', $customer_ids)
                                    ->whereMonth('created_at', $numericMonth)
                                    ->whereYear('created_at', $request->selected_year)->get();
        
        $comments = $comment_list->where('comment', '!=', '')->pluck('comment'); 
    
        $total_comments = $comment_list->where('comment', '!=', '')->count();
        $total_complaints = $comment_list->where('is_complaint', 1)->count();
    
        // Get section data if it exists
        $section = null;
        if ($section_id) {
            $section = $request->section;
        }
        
        // Get service data
        $service = $this->getServiceById($service_id);
    
        //send response to front end
        return Inertia::render('CSI/Index')
            ->with('user', $user)
            ->with('cc_data', $cc_data)
            ->with('assignatorees', $assignatorees)
            ->with('division', $request->division)
            ->with('section', $section)
            ->with('service', $service)
            ->with('services', $services)
            ->with('dimensions', $dimensions)
            ->with('respondents_list', $data)
            ->with('y_totals', $y_totals)
            ->with('grand_vs_total', $grand_vs_total)
            ->with('grand_s_total', $grand_s_total)
            ->with('grand_n_total', $grand_n_total)
            ->with('grand_d_total', $grand_d_total)
            ->with('grand_vd_total', $grand_vd_total)
            ->with('x_totals', $x_totals)
            ->with('x_grand_total', $x_grand_total)
            ->with('likert_scale_rating_totals', $likert_scale_rating_totals)
            ->with('lsr_grand_total', $lsr_grand_total)
            ->with('importance_rate_score_totals', $importance_rate_score_totals)
            ->with('x_importance_totals', $x_importance_totals)
            ->with('importance_ilsr_totals', $importance_ilsr_totals)
            ->with('gap_totals', $gap_totals)
            ->with('gap_grand_total', $gap_grand_total)
            ->with('wf_totals', $wf_totals)
            ->with('ss_totals', $ss_totals)
            ->with('ws_totals', $ws_totals)
            ->with('total_respondents', $total_respondents)
            ->with('total_vss_respondents', $total_vss_respondents)
            ->with('percentage_vss_respondents', $percentage_vss_respondents)
            ->with('customer_satisfaction_rating', $customer_satisfaction_rating)
            ->with('customer_satisfaction_index', $customer_satisfaction_index)
            ->with('net_promoter_score', $net_promoter_score)
            ->with('percentage_promoters', $percentage_promoters)
            ->with('percentage_detractors', $percentage_detractors)
            ->with('total_comments', $total_comments)
            ->with('total_complaints', $total_complaints)
            ->with('comments', $comments)
            ->with('request', $request);
    }


    public function all_sections()
    {
        // Get all divisions with their sections and services
        $divisions = Division::with([
            'sections.services', // Get services associated with sections
            'services' => function($query) {
                // Only get services directly linked to divisions (without a section)
                $query->whereNull('section_id');
            }
        ])->get();

        // Transform the data to include both section and service information
        $data = $divisions->map(function($division) {
            return [
                'id' => $division->id,
                'name' => $division->division_name,
                'sections' => $division->sections->map(function($section) {
                    return [
                        'id' => $section->id,
                        'name' => $section->section_name,
                        // Include services for each section
                        'services' => $section->services->map(function($service) {
                            return [
                                'id' => $service->id,
                                'name' => $service->service_name
                            ];
                        })
                    ];
                }),
                // Include direct services (not associated with any section)
                'direct_services' => $division->services->map(function($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->service_name
                    ];
                })
            ];
        });

        return Inertia::render('CSI/AllDivisionSections/Index')
            ->with('division_sections', $data);
    }

    public function generateAllServiceReports(Request $request)
    {
        // Check the reporting period type
        if($request->csi_type == "By Month"){
            return $this->generateCSIAllServiceMonthly($request); 
        }
        else if($request->csi_type == "By Quarter"){
            if($request->selected_quarter == "FIRST QUARTER"){
                return $this->generateCSIAllServiceFirstQuarter($request);
            }
            else if($request->selected_quarter == "SECOND QUARTER"){
                return $this->generateCSIAllServiceSecondQuarter($request);
            }
            else if($request->selected_quarter == "THIRD QUARTER"){
                return $this->generateCSIAllServiceThirdQuarter($request);
            }
            else if($request->selected_quarter == "FOURTH QUARTER"){
                return $this->generateCSIAllServiceFourthQuarter($request);
            }
        }
        else if($request->csi_type == "By Year/Annual"){
            return $this->generateCSIAllServiceYearly($request);  
        }
    }
}