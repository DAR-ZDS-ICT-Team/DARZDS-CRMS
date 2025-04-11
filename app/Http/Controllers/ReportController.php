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
use App\Http\Resources\DivisionServices as DivisionServicesResource;
use App\Http\Resources\SubServices as SubServicesResource;
use App\Models\CustomerRecommendationRating;
use App\Http\Resources\Division as DivisionResource;
use App\Http\Resources\CustomerAttributeRatings as CARResource;
use App\Models\SubServices;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        //get user
        $user = Auth::user();

        //get assignatoree list
        $assignatorees = Assignatorees::all();

        $dimensions = Dimension::all();
        $division = Division::findOrFail($request->division_id);

        $sections = [];
        $section = [];
        if($request->section_id){
            $sections = Section::where('id', $request->section_id)->get();
            $section = SectionResource::collection($sections);
        }
        
        $services = [];
        $service = [];
        
        // Check if this is a direct service to division or belongs to a section
        if($request->direct_service && $request->service_id){
            // Direct service to division
            $services = Services::where('id', $request->service_id)
                            ->where('division_id', $request->division_id)
                            ->whereNull('section_id')
                            ->get();
            $service = ServiceResource::collection($services);
        } else if($request->section_id && $request->service_id){
            // Service belongs to a section
            $services = Services::where('id', $request->service_id)
                            ->where('section_id', $request->section_id)
                            ->get();
            $service = ServiceResource::collection($services);
        } else if($request->service_id){
            // Try to find the service regardless of path
            $services = Services::where('id', $request->service_id)->get();
            $service = ServiceResource::collection($services);
        }
        
        // Handle sub_service if it exists
        $sub_services = [];
        $sub_service = [];
        if ($request->sub_service_id) {
            $sub_services = SubServices::where('id', $request->sub_service_id);
            
            // If service_id is provided, filter by service as well
            if ($request->service_id) {
                $sub_services = $sub_services->where('service_id', $request->service_id);
            }
            
            $sub_services = $sub_services->get();
            $sub_service = SubServicesResource::collection($sub_services);
        }

        return Inertia::render('CSI/Index')
            ->with('assignatorees', $assignatorees)
            ->with('dimensions', $dimensions)
            ->with('division', $division)
            ->with('section', $section)
            ->with('service', $service)
            ->with('sub_service', $sub_service)
            ->with('user', $user);
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
            return $this->generateCSIByServiceByDate($request, $user->office_id);
        }
        else if($request->csi_type == "By Month"){
            return $this->generateCSIByServiceMonthly($request, $user->office_id);
        }
        else if($request->csi_type == "By Quarter"){
            return $this->generateCSIByQuarter($request, $user->office_id);
        }
        else if($request->csi_type == "By Year/Annual"){
            return $this->generateCSIByServiceYearly($request, $user->office_id);  
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

    protected function getSubServices($request)
    {
        $sub_services = [];
        
        if ($request->sub_service_id) {
            $sub_service = SubServices::find($request->sub_service_id);
            if ($sub_service) {
                $sub_services = SubServicesResource::collection([$sub_service]);
            }
        } else if ($request->service_id) {
            $sub_services = SubServicesResource::collection(
                SubServices::where('service_id', $request->service_id)->get()
            );
        }
        
        return $sub_services;
    }


    // Get a service by ID
    protected function getServiceById($serviceId)
    {
        if (!$serviceId) {
            // Instead of returning null, return an empty ServiceResource
            return new ServiceResource(new Services());
        }
        
        $service = Services::find($serviceId);
        return $service ? new ServiceResource($service) : new ServiceResource(new Services());
    }

    protected function getSubServiceById($sub_service_id)
    {
        return SubServices::find($sub_service_id);
    }

    // Modified query method to search CSF forms with the new structure
    protected function querySearchCSF($office_id, $division_id, $section_id = null, $service_id, $sub_service_id = null, $client_type = null)
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
        ->when($sub_service_id, function ($query, $sub_service_id) {
            $query->where('sub_service_id', $sub_service_id);
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

    public function generateCSIByServiceByDate($request, $office_id)
    {
        // Get services
        $services = $this->getServices($request);

        // Get user
        $user = Auth::user();
        
        // Get assignatoree list
        $assignatorees = Assignatorees::all();
        
        $division_id = $request->division['id'];
        $service_id = $request->service_id ?? null;
        $sub_service_id = $request->sub_service_id ?? null; // Added sub_service parameter
        $client_type = $request->client_type; 

        // Modify query to handle service path with optional sub_service
        // Note: We're passing null for section_id since we're generating by service
        $customer_ids = $this->querySearchCSF($office_id, $division_id, null, $service_id, $client_type, $sub_service_id);

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

        // Calculate CC
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

        // Get service data
        $service = $this->getServiceById($service_id);
        
        // Get sub-service data if it exists
        $sub_service = null;
        if ($sub_service_id) {
            $sub_service = $this->getSubServiceById($sub_service_id);
        }

        //send response to front end
        return Inertia::render('CSI/Index')    
            ->with('user', $user)
            ->with('assignatorees', $assignatorees)
            ->with('cc_data', $cc_data) 
            ->with('services', $services)
            ->with('dimensions', $dimensions)
            ->with('division', $request->division)
            ->with('section', null) // No section since we're generating by service
            ->with('service', $service)
            ->with('sub_service', $sub_service) // Added sub_service to view
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

    public function generateCSIByServiceMonthly($request, $office_id)
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
        $service_id = $request->service_id ?? null;
        $sub_service_id = $request->sub_service_id ?? null; // Added sub_service parameter
        $client_type = $request->client_type; 

        // Modify query to handle service path with optional sub_service
        // Note: We're passing null for section_id since we're generating by service
        $customer_ids = $this->querySearchCSF($office_id, $division_id, null, $service_id, $sub_service_id, $client_type, $sub_service_id);
            
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

        // Get service data
        $service = $this->getServiceById($service_id);
        
        // Get sub-service data if it exists
        $sub_service = null;
        if ($sub_service_id) {
            $sub_service = $this->getSubServiceById($sub_service_id);
        }

        //send response to front end
        return Inertia::render('CSI/Index')
            ->with('user', $user)
            ->with('cc_data', $cc_data)
            ->with('assignatorees', $assignatorees)
            ->with('division', $request->division)
            ->with('section', null) // No section since we're generating by service
            ->with('service', $service)
            ->with('sub_service', $sub_service) // Added sub_service to view
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

    public function generateCSIByQuarter($request, $office_id)
    {
        $services = $this->getServices($request);
        $sub_services = $this->getSubServices($request);

        //get user
        $user = Auth::user();
        //get assignatoree list
        $assignatorees = Assignatorees::all();
        
        $date_range = null;
        $customer_recommendation_ratings = null;
        $respondents_list = null;
    
    
        $division_id = $request->division['id'];
        $service_id = $request->service_id;
        $sub_service_id = $request->sub_service_id; 
        $client_type = $request->client_type; 

        $startDate = null;
        $endDate = null;
        $numeric_first_month = 0;
        $numeric_second_month = 0;
        $numeric_third_month = 0;

        // Retrieve records for the specified quarter and year
        switch($request->selected_quarter){
            case 'FIRST QUARTER':
                $startDate = Carbon::create($request->selected_year, 1, 1)->startOfDay();
                $endDate = Carbon::create($request->selected_year, 3, 31)->endOfDay();

                $numeric_first_month = 1;
                $numeric_second_month = 2;
                $numeric_third_month = 3;

            break;
            case 'SECOND QUARTER':
                $startDate = Carbon::create($request->selected_year, 4, 1)->startOfDay();
                $endDate = Carbon::create($request->selected_year, 6, 30)->endOfDay();

                $numeric_first_month = 4;
                $numeric_second_month = 5;
                $numeric_third_month = 6;
            break;
            case 'THIRD QUARTER':
                $startDate = Carbon::create($request->selected_year, 7, 1)->startOfDay();
                $endDate = Carbon::create($request->selected_year, 9, 30)->endOfDay();

                $numeric_first_month = 7;
                $numeric_second_month = 8;
                $numeric_third_month = 9;
            break;
            case 'FOURTH QUARTER':
                $startDate = Carbon::create($request->selected_year, 10, 1)->startOfDay();
                $endDate = Carbon::create($request->selected_year, 12, 31)->endOfDay();

                $numeric_first_month = 10;
                $numeric_second_month = 11;
                $numeric_third_month = 12;
            break;

            default:
                dd('no quarter selected'); 
        }  
        
        // search and check list of forms query and get customers list ids
        // Pass null for section_id since we're generating by service
        $customer_ids = $this->querySearchCSF($office_id, $division_id, null, $service_id, $sub_service_id, $client_type, $sub_service_id);

        // get CC Data
        $cc_query = $this->getCitizenCharterByQuarter($request, $customer_ids, $startDate, $endDate);

        //calculate CC Data
        $cc_data = $this->calculateCC($cc_query);

        // get Customer Attribute Rating with specific quarter date range
        $date_range = $this->getCustomerAttributeRatingByQuarter($request, $customer_ids, $startDate, $endDate);

        // get first month of Specific Quarter Selected 
        $first_month = $this->getCustomerAttributeRatingByQuarterWithMonth($request, $customer_ids, $numeric_first_month);
        // get second month of Specific Quarter Selected 
        $second_month = $this->getCustomerAttributeRatingByQuarterWithMonth($request, $customer_ids, $numeric_second_month);
        // get third month of Specific Quarter Selected 
        $third_month = $this->getCustomerAttributeRatingByQuarterWithMonth($request, $customer_ids, $numeric_third_month);

        // get Customer Recommendation Rating with specific quarter date range 
        $customer_recommendation_ratings = $this->getCustomerRecommendationRatingByQuarter($request, $customer_ids, $startDate, $endDate);

        // get First Month Customer Recommendation Rating with specific quarter date range 
        $first_month_crr = $this->getCustomerRecommendationRatingByQuarterWithMonth($request, $customer_ids, $numeric_first_month);
        // get First Month Customer Recommendation Rating with specific quarter date range 
        $second_month_crr = $this->getCustomerRecommendationRatingByQuarterWithMonth($request, $customer_ids, $numeric_second_month);
        // get First Month Customer Recommendation Rating with specific quarter date range 
        $third_month_crr = $this->getCustomerRecommendationRatingByQuarterWithMonth($request, $customer_ids, $numeric_third_month);

        // get respondents list
        $respondents_list = $this->getRespondents($request, $customer_ids, $startDate, $endDate);            

        $dimensions = Dimension::all();
        $dimension_count = $dimensions->count();
        $grand_total_raw_points = 0;
        $vs_grand_total_score = 0;
        $s_grand_total_score = 0;
        $ndvd_grand_total_score = 0;
        $grand_total_score = 0;

        $vs_grand_total_raw_points = 0;
        $s_grand_total_raw_points = 0;
        $ndvd_grand_total_raw_points = 0;
        $lsr_grand_total = 0;
        $lsr_average = 0;

        //Importance total raw points  
        $vi_grand_total_raw_points = 0;
        $i_grand_total_raw_points = 0;
        $misinai_grand_total_raw_points = 0;

        //Importance grand total score
        $vi_grand_total_score = 0;
        $i_grand_total_score = 0;
        $misinai_grand_total_score = 0;

        $first_month_vs_grand_total = 0;
        $second_month_vs_grand_total = 0;
        $third_month_vs_grand_total = 0;

        $first_month_s_grand_total = 0;
        $second_month_s_grand_total = 0;
        $third_month_s_grand_total = 0;

        $first_month_ndvd_grand_total = 0;
        $second_month_ndvd_grand_total = 0;
        $third_month_ndvd_grand_total = 0;

        $first_month_na_grand_total = 0;
        $second_month_na_grand_total = 0;
        $third_month_na_grand_total = 0;

        for ($dimensionId = 1; $dimensionId <= $dimension_count; $dimensionId++) {
            //PART I

            // First Month total responses with its dimensions and rate score
            $first_month_na_total = $first_month->where('rate_score', 6)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();

            $first_month_vs_total = $first_month->where('rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_s_total = $first_month->where('rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_n_total = $first_month->where('rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_d_total = $first_month->where('rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_vd_total = $first_month->where('rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $first_month_grand_total = $first_month_vs_total + $first_month_s_total + $first_month_n_total + $first_month_d_total + $first_month_vd_total; 

            // Second Month total responses with its dimensions and rate score
            $second_month_na_total = $second_month->where('rate_score', 6)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();

            $second_month_vs_total = $second_month->where('rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_s_total = $second_month->where('rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_n_total = $second_month->where('rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_d_total = $second_month->where('rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_vd_total = $second_month->where('rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $second_month_grand_total = $second_month_vs_total + $second_month_s_total + $second_month_n_total + $second_month_d_total + $second_month_vd_total; 

            // Third Month total responses with its dimensions and rate score
            $third_month_na_total = $third_month->where('rate_score', 6)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();

            $third_month_vs_total = $third_month->where('rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_s_total = $third_month->where('rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_n_total = $third_month->where('rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_d_total = $third_month->where('rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_vd_total = $third_month->where('rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $third_month_grand_total = $third_month_vs_total + $third_month_s_total + $third_month_n_total + $third_month_d_total + $third_month_vd_total; 

            // Quarterly Very Satisfied total with specific dimension or attribute
            $vs_totals[$dimensionId] = [
                'first_month_vs_total' => $first_month_vs_total,
                'second_month_vs_total' => $second_month_vs_total,
                'third_month_vs_total' => $third_month_vs_total,
            ];
            // Quarterly Satisfied total with specific dimension or attribute
            $s_totals[$dimensionId] = [
                'first_month_s_total' => $first_month_s_total,
                'second_month_s_total' => $second_month_s_total,
                'third_month_s_total' => $third_month_s_total,
            ];

            // Quarterly Neutral total with specific dimension or attribute
            $n_totals[$dimensionId] = [
                'first_month_n_total' => $first_month_n_total,
                'second_month_n_total' => $second_month_n_total,
                'third_month_n_total' => $third_month_n_total,
            ];

            // Quarterly Disatisfied total with specific dimension or attribute
            $d_totals[$dimensionId] = [
                'first_month_d_total' => $first_month_d_total,
                'second_month_d_total' => $second_month_d_total,
                'third_month_d_total' => $third_month_d_total,
            ];

            // Quarterly Very Disatisfied total with specific dimension or attribute
            $vd_totals[$dimensionId] = [
                'first_month_vd_total' => $first_month_vd_total,
                'second_month_vd_total' => $second_month_vd_total,
                'third_month_vd_total' => $third_month_vd_total,
            ];

            // Quarterly grand totals with specific dimension or attribute
            $grand_totals[$dimensionId] = [
                'first_month_grand_total' => $first_month_grand_total,
                'second_month_grand_total' => $second_month_grand_total,
                'third_month_grand_total' => $third_month_grand_total,
            ];

            //Total Raw Points totals
            $vs_total_raw_points = $first_month_vs_total + $second_month_vs_total + $third_month_vs_total;
            $s_total_raw_points = $first_month_s_total + $second_month_s_total + $third_month_s_total;
            $n_total_raw_points = $first_month_n_total + $second_month_n_total + $third_month_n_total;
            $d_total_raw_points = $first_month_d_total + $second_month_d_total + $third_month_d_total;
            $vd_total_raw_points = $first_month_vd_total + $second_month_vd_total + $third_month_vd_total;
            $total_raw_points = $vs_total_raw_points + $s_total_raw_points + $n_total_raw_points + $d_total_raw_points + $vd_total_raw_points;           

            $vs_grand_total_raw_points += $vs_total_raw_points;
            $s_grand_total_raw_points += $s_total_raw_points;

            $ndvd_grand_total_raw_points += $n_total_raw_points + $d_total_raw_points + $vd_total_raw_points;
            $grand_total_raw_points += $total_raw_points;

            $trp_totals[$dimensionId] = [
                'vs_total_raw_points' => $vs_total_raw_points,
                's_total_raw_points' => $s_total_raw_points,
                'n_total_raw_points' => $n_total_raw_points,
                'd_total_raw_points' => $d_total_raw_points,
                'vd_total_raw_points' => $vd_total_raw_points,
                'total_raw_points' => $total_raw_points,
            ];

            //Part 1 Quarter 1 total scores
            $x_vs_total = $vs_total_raw_points * 5; 
            $x_s_total = $s_total_raw_points * 4; 
            $x_n_total = $n_total_raw_points * 3; 
            $x_d_total = $d_total_raw_points * 3; 
            $x_vd_total = $vd_total_raw_points * 1; 
            $x_total_score = $x_vs_total + $x_s_total + $x_n_total + $x_d_total + $x_vd_total;
            
            $vs_grand_total_score += $x_vs_total;
            $s_grand_total_score += $x_s_total;
            $ndvd_grand_total_score += $x_n_total + $x_d_total + $x_vd_total;
            $grand_total_score += $x_total_score;

            $p1_total_scores[$dimensionId] = [ 
                'x_vs_total' => $x_vs_total,
                'x_s_total' => $x_s_total,
                'x_n_total' => $x_n_total,
                'x_d_total' => $x_d_total,
                'x_vd_total' => $x_vd_total, 
                'x_total_score' => $x_total_score,
            ];

            // Likert Scale Rating = total score / grand total of total raw points
            if($total_raw_points != 0) {
                $vs_lsr_total = number_format(($x_vs_total / $total_raw_points), 2);
                $s_lsr_total = number_format(($x_s_total / $total_raw_points), 2);
                $n_lsr_total = number_format(($x_n_total / $total_raw_points), 2);
                $d_lsr_total = number_format(($x_d_total / $total_raw_points), 2);
                $vd_lsr_total = number_format(($x_vd_total / $total_raw_points), 2);
                $lsr_total = number_format(($vs_lsr_total + $s_lsr_total + $n_lsr_total + $d_lsr_total + $vd_lsr_total), 2);
                $lsr_grand_total += $lsr_total;
                $lsr_grand_total = number_format(($lsr_grand_total), 2);
                $lsr_average = number_format(($lsr_grand_total / $dimensionId), 2);
            }
            else {
                $vs_lsr_total = 0;
                $s_lsr_total = 0;
                $n_lsr_total = 0;
                $d_lsr_total = 0;
                $vd_lsr_total = 0;
                $lsr_total = 0;
                $lsr_grand_total = 0;
                $lsr_average = 0;
            }

            $lsr_totals[$dimensionId] = [
                'vs_lsr_total' => $vs_lsr_total,
                's_lsr_total' => $s_lsr_total,
                'n_lsr_total' => $n_lsr_total,
                'd_lsr_total' => $d_lsr_total,
                'vd_lsr_total' => $vd_lsr_total,
                'lsr_total' => $lsr_total,
            ];
            
            // PART II
            // first month total responses with its dimensions and rate score
            $first_month_vi_total = $first_month->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_i_total = $first_month->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_mi_total = $first_month->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_si_total = $first_month->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $first_month_nai_total = $first_month->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $first_month_i_grand_total = $first_month_vi_total + $first_month_i_total + $first_month_mi_total + $first_month_si_total + $first_month_nai_total; 

            //  second_month importance total responses with its dimensions and rate score
            $second_month_vi_total = $second_month->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_i_total = $second_month->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_mi_total = $second_month->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_si_total = $second_month->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $second_month_nai_total = $second_month->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $second_month_i_grand_total = $second_month_vi_total + $second_month_i_total + $second_month_mi_total + $second_month_si_total + $second_month_nai_total; 

            //  third month total responses with its dimensions and rate score
            $third_month_vi_total = $third_month->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_i_total = $third_month->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_mi_total = $third_month->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_si_total = $third_month->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $third_month_nai_total = $third_month->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $third_month_i_grand_total = $third_month_vi_total + $third_month_i_total + $third_month_mi_total + $third_month_si_total + $third_month_nai_total; 

                // Very Important total with specific dimention or attribute
                $vi_totals[$dimensionId] = [
                    'first_month_vi_total' => $first_month_vi_total,
                    'second_month_vi_total' => $second_month_vi_total,
                    'third_month_vi_total' => $third_month_vi_total,
                ];
                //Important total with specific dimention or attribute
                $i_totals[$dimensionId] = [
                    'first_month_i_total' => $first_month_i_total,
                    'second_month_i_total' => $second_month_i_total,
                    'third_month_i_total' => $third_month_i_total,
                ];
                // Moderately Important total with specific dimention or attribute
                $mi_totals[$dimensionId] = [
                    'first_month_mi_total' => $first_month_mi_total,
                    'second_month_mi_total' => $second_month_mi_total,
                    'third_month_mi_total' => $third_month_mi_total,
                ];
                // slightly Important total with specific dimention or attribute
                $si_totals[$dimensionId] = [
                    'first_month_si_total' => $first_month_si_total,
                    'second_month_si_total' => $second_month_si_total,
                    'third_month_si_total' => $third_month_si_total,
                ];

                $nai_totals[$dimensionId] = [
                    'first_month_nai_total' => $first_month_nai_total,
                    'second_month_nai_total' => $second_month_nai_total,
                    'third_month_nai_total' => $third_month_nai_total,
                ];

                $i_grand_totals[$dimensionId] = [
                    'first_month_i_grand_total' => $first_month_i_grand_total,
                    'second_month_i_grand_total' => $second_month_i_grand_total,
                    'third_month_i_grand_total' => $third_month_i_grand_total,
                ];

                
            //Importance Total Raw Points totals
            $vi_total_raw_points = $first_month_vi_total + $second_month_vi_total + $third_month_vi_total;
            $i_total_raw_points = $first_month_i_total + $second_month_i_total + $third_month_i_total;
            $mi_total_raw_points = $first_month_mi_total + $second_month_mi_total + $third_month_mi_total;
            $si_total_raw_points = $first_month_si_total + $second_month_si_total + $third_month_si_total;
            $nai_total_raw_points = $first_month_nai_total + $second_month_nai_total + $third_month_nai_total;
            $total_raw_points = $vi_total_raw_points + $i_total_raw_points + $mi_total_raw_points + $si_total_raw_points + $nai_total_raw_points;           

            $vi_grand_total_raw_points += $vi_total_raw_points;
            $i_grand_total_raw_points += $i_total_raw_points;
            $misinai_grand_total_raw_points += $mi_total_raw_points + $si_total_raw_points + $nai_total_raw_points;
            $grand_total_raw_points += $total_raw_points;

        
            $i_trp_totals[$dimensionId] = [
                'vi_total_raw_points' => $vi_total_raw_points,
                'i_total_raw_points' => $i_total_raw_points,
                'mi_total_raw_points' => $mi_total_raw_points,
                'si_total_raw_points' => $si_total_raw_points,
                'nai_total_raw_points' => $nai_total_raw_points,
                'total_raw_points' => $total_raw_points,
            ];

        

            //Part 1 Quarter 1 total scores
            $x_vi_total = $vi_total_raw_points * 5; 
            $x_i_total = $i_total_raw_points * 4; 
            $x_mi_total = $mi_total_raw_points * 3; 
            $x_si_total = $si_total_raw_points * 3; 
            $x_nai_total = $nai_total_raw_points * 1; 
            $x_i_total_score = $x_vi_total + $x_i_total + $x_mi_total + $x_si_total + $x_nai_total;
            

            $vi_grand_total_score += $x_vi_total;
            $i_grand_total_score += $x_si_total;
            $misinai_grand_total_score += $x_mi_total + $x_si_total + $x_nai_total;

            $i_total_scores[$dimensionId] = [ 
                'x_vi_total' => $x_vi_total,
                'x_i_total' => $x_i_total,
                'x_mi_total' => $x_mi_total,
                'x_si_total' => $x_si_total,
                'x_nai_total' => $x_nai_total, 
                'x_i_total_score' => $x_i_total_score,
            ];

            // Calculate all respondents who rated VS 
            $first_month_vs_grand_total += $first_month_vs_total;
            $second_month_vs_grand_total += $second_month_vs_total;
            $third_month_vs_grand_total += $third_month_vs_total; 

            // Calculate all respondents who rated S
            $first_month_s_grand_total += $first_month_s_total;
            $second_month_s_grand_total += $second_month_s_total;
            $third_month_s_grand_total += $third_month_s_total; 

            // Calculate all respondents who rated NDVD
            $first_month_ndvd_total = $first_month_n_total + $first_month_d_total + $first_month_vd_total;
            $second_month_ndvd_total = $second_month_n_total + $second_month_d_total + $second_month_vd_total;
            $third_month_ndvd_total = $third_month_n_total + $third_month_d_total + $third_month_vd_total;

            $first_month_ndvd_grand_total += $first_month_ndvd_total;
            $second_month_ndvd_grand_total += $second_month_ndvd_total;
            $third_month_ndvd_grand_total += $third_month_ndvd_total; 

            // Calculate all respondents who rated n/a
            $first_month_na_grand_total += $first_month_na_total;
            $second_month_na_grand_total += $second_month_na_total;
            $third_month_na_grand_total += $third_month_na_total; 

        }


        // Calculate all respondents 
        $first_month_grand_total = $first_month_vs_grand_total + $first_month_s_grand_total + $first_month_ndvd_grand_total;     
        $second_month_grand_total = $second_month_vs_grand_total + $second_month_s_grand_total + $second_month_ndvd_grand_total;
        $third_month_grand_total = $third_month_vs_grand_total + $third_month_s_grand_total + $third_month_ndvd_grand_total;

        //Calculate total number of respondents/customer who rated VS/S include N/A
        // Formula ----> get the sum of total respondents for each dimension who rated VS or S and divide it to dimension total count
        // here is 9 because I include the overall data in the dimensions

        $first_month_vss_total = $first_month_vs_grand_total + $first_month_s_grand_total + $first_month_na_grand_total;
        $first_month_total_vss_respondents = $first_month_vss_total / 9;     
        $first_month_total_vss_respondents = round($first_month_total_vss_respondents);  

        $second_month_vss_total = $second_month_vs_grand_total + $second_month_s_grand_total + $second_month_na_grand_total;
        $second_month_total_vss_respondents = $second_month_vss_total / 9;     
        $second_month_total_vss_respondents = round($second_month_total_vss_respondents);  

        $third_month_vss_total = $third_month_vs_grand_total + $third_month_s_grand_total + $third_month_na_grand_total;
        $third_month_total_vss_respondents = $third_month_vss_total / 9;     
        $third_month_total_vss_respondents = round($third_month_total_vss_respondents);  

        $total_vss_respondents = $first_month_total_vss_respondents + $second_month_total_vss_respondents + $third_month_total_vss_respondents;

        // Total No. of Very Satisfied (VS) Responses of First Quarter
        // -- 1st month
        $first_month_total_vs_respondents = $first_month->where('rate_score', 5)->groupBy('customer_id')->count();
        // -- 2nd month
        $second_month_total_vs_respondents = $second_month->where('rate_score', 5)->groupBy('customer_id')->count();
        // -- 3rd month
        $third_month_total_vs_respondents = $third_month->where('rate_score', 5)->groupBy('customer_id')->count();

        // Total No. of Satisfied (S) Responses
        // -- first month
        $first_month_total_s_respondents = $first_month->where('rate_score', 4)->groupBy('customer_id')->count();
        // -- second month
        $second_month_total_s_respondents = $second_month->where('rate_score', 4)->groupBy('customer_id')->count();
        // -- third month
        $third_month_total_s_respondents = $third_month->where('rate_score', 4)->groupBy('customer_id')->count();

        // Total No. of Other (N, D, VD) Responses
        // -- first month
        $first_month_total_ndvd_respondents = $first_month->where('rate_score', '<', 4)->groupBy('customer_id')->count();
        // -- second month
        $second_month_total_ndvd_respondents = $second_month->where('rate_score', '<', 4)->groupBy('customer_id')->count();
        // -- third month
        $third_month_total_ndvd_respondents = $third_month->where('rate_score', '<', 4)->groupBy('customer_id')->count();
        
        // Total No. of All Responses
        // -- first month
        $first_month_total_respondents = $first_month->groupBy('customer_id')->count();
        // -- second month
        $second_month_total_respondents = $second_month->groupBy('customer_id')->count();
        // -- third month
        $third_month_total_respondents = $third_month->groupBy('customer_id')->count();

        // Total respondents /Customers
        $total_respondents = $date_range->groupBy('customer_id')->count();

        // Quarter total number of promoters or respondents who rated 9-10 in recommendation rating
        $total_promoters = $customer_recommendation_ratings->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        // 1st month
        $first_month_total_promoters = $first_month_crr->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        // 2nd Month
        $second_month_total_promoters = $second_month_crr->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        // 3rd month
        $third_month_total_promoters = $third_month_crr->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        
        // total number of detractors or respondents who rated 0-6 in recommendation rating
        $total_detractors = $customer_recommendation_ratings->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();
        // 1st month
        $first_month_total_detractors = $first_month_crr->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();
        // 2nd Month
        $second_month_total_detractors = $second_month_crr->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();
        // 3rd month
        $third_month_total_detractors = $third_month_crr->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();

        //Percentage of Respondents/Customers who rated VS/S = total no. of respondents / total no. respondets who rated vs/s * 100
        $percentage_vss_respondents = 0;
        if($total_respondents != 0) {
            $percentage_vss_respondents = ($total_vss_respondents / $total_respondents) * 100;
        }
        $percentage_vss_respondents = number_format($percentage_vss_respondents, 2);

        $customer_satisfaction_rating = 0;
        if($total_vss_respondents != 0) {
            $customer_satisfaction_rating = (($vs_grand_total_score + $s_grand_total_score) / $grand_total_score) * 100;
        }
        $customer_satisfaction_rating = number_format($customer_satisfaction_rating, 2);

        //Percentage of Promoters = number of promoters / total respondents
        $percentage_promoters = 0;
        //  Percentage promoters
        $first_month_percentage_promoters = 0.00;
        $second_month_percentage_promoters = 0.00;
        $third_month_percentage_promoters = 0.00;

        // Percentage of promoter
        $first_month_percentage_detractors = 0.00;
        $second_month_percentage_detractors = 0.00;
        $third_month_percentage_detractors = 0.00;

        //nps
        $first_month_net_promoter_score = 0.00;
        $second_month_net_promoter_score = 0.00;
        $third_month_net_promoter_score = 0.00;

        //average
        $ave_net_promoter_score = 0.00;
        $average_percentage_promoters = 0.00;
        $average_percentage_detractors = 0.00;


        if($total_respondents != 0) {
            $percentage_promoters = number_format((($total_promoters / $total_respondents) * 100), 2);
            if($first_month_total_respondents != 0) {
                $first_month_percentage_promoters = number_format((($first_month_total_promoters / $first_month_total_respondents) * 100), 2);
                $first_month_percentage_detractors = number_format((($first_month_total_detractors / $first_month_total_respondents) * 100), 2);
            }

            if($second_month_total_respondents != 0) {
                $second_month_percentage_promoters = number_format((($second_month_total_promoters / $second_month_total_respondents) * 100), 2);
                $second_month_percentage_detractors = number_format((($second_month_total_detractors / $second_month_total_respondents) * 100), 2);
            }
        
            if($third_month_total_respondents != 0) {
                $third_month_percentage_promoters = number_format((($third_month_total_promoters / $third_month_total_respondents) * 100), 2);
                $third_month_percentage_detractors = number_format((($third_month_total_detractors / $third_month_total_respondents) * 100), 2);
            }
        
        
            //Percentage of Promoters = number of promoters / total respondents
            $percentage_detractors = number_format((($total_detractors / $total_respondents) * 100), 2);

            // Net Promotion Scores(NPS) = Percentage of Promoters−Percentage of Detractors
            $first_month_net_promoter_score = number_format(($first_month_percentage_promoters - $first_month_percentage_detractors), 2);
            $second_month_net_promoter_score = number_format(($second_month_percentage_promoters - $second_month_percentage_detractors), 2);
            $third_month_net_promoter_score = number_format(($third_month_percentage_promoters - $third_month_percentage_detractors), 2);

            $ave_net_promoter_score = number_format((($first_month_net_promoter_score + $second_month_net_promoter_score + $third_month_net_promoter_score) / 3), 2);
            $average_percentage_promoters = number_format((($first_month_percentage_promoters + $second_month_percentage_promoters + $third_month_percentage_promoters) / 3), 2);
            $average_percentage_detractors = number_format((($first_month_percentage_detractors + $second_month_percentage_detractors + $third_month_percentage_detractors) / 3), 2);
        }

        // get Monthly CSI 
        
        $first_month_csi = $this->getMonthlyCSI($request, $office_id, $numeric_first_month);
        $second_month_csi = $this->getMonthlyCSI($request, $office_id, $numeric_second_month);
        $third_month_csi = $this->getMonthlyCSI($request, $office_id, $numeric_third_month);

        $customer_satisfaction_index = number_format((($first_month_csi + $second_month_csi + $third_month_csi) / 3), 2);

        if($customer_satisfaction_index > 100) {
            $customer_satisfaction_index = number_format(100, 2);
        }

        //comments and complaints
        $comment_list = CustomerComment::whereIn('customer_id', $customer_ids)
                                        ->whereBetween('created_at', [$startDate, $endDate])
                                        ->whereYear('created_at', $request->selected_year)->get();

        $comments = $comment_list->where('comment', '!=', '')->pluck('comment'); 

        $total_comments = $comment_list->where('comment', '!=', '')->count();
        $total_complaints = $comment_list->where('is_complaint', 1)->count();

        //Respondents list
        $data = CARResource::collection($respondents_list);

        // Get service data
        $service = $this->getServiceById($service_id);
        
        // Get sub-service data if it exists
        $sub_service = null;
        if ($sub_service_id) {
            $sub_service = $this->getSubServiceById($sub_service_id);
        }

        //send response to front end
        return Inertia::render('CSI/Index')
            ->with('cc_data', $cc_data)
            ->with('user', $user)
            ->with('assignatorees', $assignatorees)
            ->with('services', $services)
            ->with('sub_services', $sub_services)
            ->with('dimensions', $dimensions)
            ->with('division', $request->division)
            ->with('section', null) 
            ->with('service', $service)
            ->with('sub_service', $sub_service) 
            ->with('respondents_list', $data)
            ->with('trp_totals', $trp_totals)
            ->with('grand_total_raw_points', $grand_total_raw_points)
            ->with('vs_grand_total_raw_points', $vs_grand_total_raw_points)
            ->with('s_grand_total_raw_points', $s_grand_total_raw_points)
            ->with('ndvd_grand_total_raw_points', $ndvd_grand_total_raw_points)
            ->with('p1_total_scores', $p1_total_scores)
            ->with('vs_grand_total_score', $vs_grand_total_score) 
            ->with('s_grand_total_score', $s_grand_total_score)
            ->with('ndvd_grand_total_score', $ndvd_grand_total_score) 
            ->with('grand_total_score', $grand_total_score) 
            ->with('lsr_totals', $lsr_totals)
            ->with('lsr_grand_total', $lsr_grand_total)
            ->with('lsr_average', $lsr_average) 
            ->with('vs_totals', $vs_totals)
            ->with('s_totals', $s_totals)
            ->with('n_totals', $n_totals)
            ->with('d_totals', $d_totals)
            ->with('vd_totals', $vd_totals)
            ->with('grand_totals', $grand_totals)
            ->with('first_month_total_vs_respondents', $first_month_total_vs_respondents)
            ->with('second_month_total_vs_respondents', $second_month_total_vs_respondents)
            ->with('third_month_total_vs_respondents', $third_month_total_vs_respondents)
            ->with('first_month_total_s_respondents', $first_month_total_s_respondents)
            ->with('second_month_total_s_respondents', $second_month_total_s_respondents)
            ->with('third_month_total_s_respondents', $third_month_total_s_respondents)
            ->with('first_month_total_ndvd_respondents', $first_month_total_ndvd_respondents)
            ->with('second_month_total_ndvd_respondents', $second_month_total_ndvd_respondents)
            ->with('third_month_total_ndvd_respondents', $third_month_total_ndvd_respondents)
            ->with('first_month_total_respondents', $first_month_total_respondents)
            ->with('second_month_total_respondents', $second_month_total_respondents)
            ->with('third_month_total_respondents', $third_month_total_respondents)
            ->with('total_respondents', $total_respondents)
            ->with('total_vss_respondents', $total_vss_respondents)
            ->with('percentage_vss_respondents', $percentage_vss_respondents)
            ->with('total_promoters', $total_promoters)
            ->with('total_detractors', $total_detractors)
            ->with('vi_totals', $vi_totals)
            ->with('i_totals', $i_totals)
            ->with('mi_totals', $mi_totals)
            ->with('si_totals', $si_totals)
            ->with('nai_totals', $nai_totals)
            ->with('i_grand_totals', $i_grand_totals)
            ->with('i_trp_totals', $i_trp_totals)
            ->with('i_grand_total_raw_points', $i_grand_total_raw_points)
            ->with('vi_grand_total_raw_points', $vi_grand_total_raw_points)
            ->with('s_grand_total_raw_points', $s_grand_total_raw_points)
            ->with('misinai_grand_total_raw_points', $misinai_grand_total_raw_points)
            ->with('i_total_scores', $i_total_scores)
            ->with('vi_grand_total_score', $vi_grand_total_score) 
            ->with('i_grand_total_score', $i_grand_total_score) 
            ->with('misinai_grand_total_score', $misinai_grand_total_score)
            ->with('percentage_promoters', $percentage_promoters)
            ->with('first_month_percentage_promoters', $first_month_percentage_promoters)
            ->with('second_month_percentage_promoters', $second_month_percentage_promoters)
            ->with('third_month_percentage_promoters', $third_month_percentage_promoters)
            ->with('average_percentage_promoters', $average_percentage_promoters)
            ->with('first_month_percentage_detractors', $first_month_percentage_detractors)
            ->with('second_percentage_detractors', $second_month_percentage_detractors)
            ->with('third_month_percentage_detractors', $third_month_percentage_detractors) 
            ->with('average_percentage_detractors', $average_percentage_detractors)
            ->with('first_month_net_promoter_score', $first_month_net_promoter_score)
            ->with('second_month_net_promoter_score', $second_month_net_promoter_score)
            ->with('third_month_net_promoter_score', $third_month_net_promoter_score)
            ->with('ave_net_promoter_score', $ave_net_promoter_score)
            ->with('customer_satisfaction_rating', $customer_satisfaction_rating)
            ->with('csi', $customer_satisfaction_index)
            ->with('first_month_csi', $first_month_csi)
            ->with('second_month_csi', $second_month_csi)
            ->with('third_month_csi', $third_month_csi)
            ->with('first_month_vs_grand_total', $first_month_vs_grand_total)
            ->with('second_month_vs_grand_total', $second_month_vs_grand_total)
            ->with('third_month_vs_grand_total', $third_month_vs_grand_total)
            ->with('first_month_s_grand_total', $first_month_s_grand_total)
            ->with('second_month_s_grand_total', $second_month_s_grand_total)
            ->with('third_month_s_grand_total', $third_month_s_grand_total)
            ->with('first_month_ndvd_grand_total', $first_month_ndvd_grand_total)
            ->with('second_month_ndvd_grand_total', $second_month_ndvd_grand_total)
            ->with('third_month_ndvd_grand_total', $third_month_ndvd_grand_total)
            ->with('first_month_grand_total', $first_month_grand_total)
            ->with('second_month_grand_total', $second_month_grand_total)
            ->with('third_month_grand_total', $third_month_grand_total)
            ->with('total_comments', $total_comments)
            ->with('total_complaints', $total_complaints)
            ->with('comments', $comments)
            ->with('request', $request);
    }

    public function generateCSIByServiceYearly($request, $office_id)
    {
        $services = $this->getServices($request);
        $sub_services = $this->getSubServices($request);

        //get user
        $user = Auth::user();
        //get assignatoree list
        $assignatorees = Assignatorees::all();

        $date_range = [];
        $q1_date_range = [];
        $q2_date_range = [];
        $q3_date_range = [];
        $q4_date_range = [];
        $customer_recommendation_ratings = null;
        $respondents_list = null;
        
        $division_id = $request->division['id'];
        $service_id = $request->service_id;
        $sub_service_id = $request->sub_service_id; // Changed from sub_section_id to sub_service_id
        $client_type = $request->client_type; 

        // search and check list of forms query  
        // Pass null for section_id since we're generating by service
        $customer_ids = $this->querySearchCSF($office_id, $division_id, null, $service_id, $client_type, $sub_service_id);
                
            // Citizen's Charter
            $cc_query = CustomerCCRating::whereYear('created_at', $request->selected_year)
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

        //calculate CC
        $cc_data = $this->calculateCC($cc_query);

        // Retrieve records for the specified quarter and year
        $q1_start_date = Carbon::create($request->selected_year, 1, 1)->startOfDay();
        $q1_end_date = Carbon::create($request->selected_year, 3, 31)->endOfDay();

        $q2_start_date = Carbon::create($request->selected_year, 4, 1)->startOfDay();
        $q2_end_date = Carbon::create($request->selected_year, 6, 31)->endOfDay();

        $q3_start_date = Carbon::create($request->selected_year, 7, 1)->startOfDay();
        $q3_end_date = Carbon::create($request->selected_year, 9, 31)->endOfDay();

        $q4_start_date = Carbon::create($request->selected_year, 10, 1)->startOfDay();
        $q4_end_date = Carbon::create($request->selected_year, 12, 31)->endOfDay();

        $q1_date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q1_start_date, $q1_end_date])
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
        $q2_date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q2_start_date, $q2_end_date])
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
        $q3_date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q3_start_date, $q3_end_date])
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
        $q4_date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q4_start_date, $q4_end_date])
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
        
        $date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
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

        $customer_recommendation_ratings = CustomerRecommendationRating::whereYear('created_at', $request->selected_year)->get();

        $q1_crr = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q1_start_date, $q1_end_date])
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

        $q2_crr = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q2_start_date, $q2_end_date])
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
                                                    
        $q3_crr = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q3_start_date, $q3_end_date])
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

        $q4_crr = CustomerRecommendationRating::whereIn('customer_id', $customer_ids)
                                                ->whereBetween('created_at', [$q4_start_date, $q4_end_date])
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


        $respondents_list = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
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
        
        

        $dimensions = Dimension::all();
        $dimension_count = $dimensions->count();
        $grand_total_raw_points = 0;
        $vs_grand_total_score = 0;
        $s_grand_total_score = 0;
        $ndvd_grand_total_score = 0;
        $grand_total_score = 0;

        $vs_grand_total_raw_points = 0;
        $s_grand_total_raw_points = 0;
        $ndvd_grand_total_raw_points = 0;
        $lsr_grand_total = 0;
        $lsr_average = 0;

        //Importance total raw points  
        $vi_grand_total_raw_points = 0;
        $i_grand_total_raw_points = 0;
        $misinai_grand_total_raw_points = 0;
        //Importance grand total score
        $vi_grand_total_score = 0;
        $i_grand_total_score = 0;
        $misinai_grand_total_score = 0;
        $overall_total_scores = 0;

        for ($dimensionId = 1; $dimensionId <= $dimension_count; $dimensionId++) {
            //PART I

            //  Quarter 1  with specific year total responses with its dimensions and rate score
            $q1_vs_total = $q1_date_range->where('rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_s_total = $q1_date_range->where('rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_n_total = $q1_date_range->where('rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_d_total = $q1_date_range->where('rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_vd_total = $q1_date_range->where('rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q1_grand_total = $q1_vs_total + $q1_s_total + $q1_n_total + $q1_d_total + $q1_vd_total; 
    
            //  Quarter 2  with specific year total responses with its dimensions and rate score
            $q2_vs_total = $q2_date_range->where('rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_s_total = $q2_date_range->where('rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_n_total = $q2_date_range->where('rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_d_total = $q2_date_range->where('rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_vd_total = $q2_date_range->where('rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q2_grand_total = $q2_vs_total + $q2_s_total + $q2_n_total + $q2_d_total + $q2_vd_total; 

            //  Quarter 3  with specific year total responses with its dimensions and rate score
            $q3_vs_total = $q3_date_range->where('rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_s_total = $q3_date_range->where('rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_n_total = $q3_date_range->where('rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_d_total = $q3_date_range->where('rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_vd_total = $q3_date_range->where('rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q3_grand_total = $q3_vs_total + $q3_s_total + $q3_n_total + $q3_d_total + $q3_vd_total; 
    
            //  Quarter 4  with specific year total responses with its dimensions and rate score
            $q4_vs_total = $q4_date_range->where('rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_s_total = $q4_date_range->where('rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_n_total = $q4_date_range->where('rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_d_total = $q4_date_range->where('rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_vd_total = $q4_date_range->where('rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q4_grand_total = $q4_vs_total + $q4_s_total + $q4_n_total + $q4_d_total + $q4_vd_total; 
            

            // Quarters Very Satisfied total with specific dimention or attribute
            $vs_totals[$dimensionId] = [
                'q1_vs_total' => $q1_vs_total,
                'q2_vs_total' => $q2_vs_total,
                'q3_vs_total' => $q3_vs_total,
                'q4_vs_total' => $q4_vs_total,
            ];
            // Quarters Satisfied total with specific dimention or attribute
            $s_totals[$dimensionId] = [
                'q1_s_total' => $q1_s_total,
                'q2_s_total' => $q2_s_total,
                'q3_s_total' => $q3_s_total,
                'q4_s_total' => $q4_s_total,
            ];
            // Quarters Neither total with specific dimention or attribute
            $n_totals[$dimensionId] = [
                'q1_n_total' => $q1_n_total,
                'q2_n_total' => $q2_n_total,
                'q3_n_total' => $q3_n_total,
                'q4_n_total' => $q4_n_total,
            ];
            // Quarters Dissatisfied total with specific dimention or attribute
            $d_totals[$dimensionId] = [
                'q1_d_total' => $q1_d_total,
                'q2_d_total' => $q2_d_total,
                'q3_d_total' => $q3_d_total,
                'q4_d_total' => $q4_d_total,
            ];
            // Quarters Very Dissatisfied total with specific dimention or attribute
            $vd_totals[$dimensionId] = [
                'q1_vd_total' => $q1_vd_total,
                'q2_vd_total' => $q2_vd_total,
                'q3_vd_total' => $q3_vd_total,
                'q4_vd_total' => $q4_vd_total,
            ];

            // Quarters grand total with specific dimention or attribute
            $grand_totals[$dimensionId] = [
                'q1_grand_total' => $q1_grand_total,
                'q2_grand_total' => $q2_grand_total,
                'q3_grand_total' => $q3_grand_total,
                'q4_grand_total' => $q4_grand_total,
            ];

            //Total Raw Points totals
            $vs_total_raw_points = $q1_vs_total + $q2_vs_total + $q3_vs_total + $q4_vs_total;
            $s_total_raw_points = $q1_s_total + $q2_s_total + $q3_s_total + $q4_s_total;
            $n_total_raw_points = $q1_n_total + $q2_n_total + $q3_n_total + $q4_n_total;
            $d_total_raw_points = $q1_n_total + $q2_n_total + $q3_n_total + $q4_n_total;
            $vd_total_raw_points = $q1_vd_total + $q2_vd_total + $q3_vd_total + $q4_vd_total;
            $total_raw_points = $vs_total_raw_points + $s_total_raw_points + $n_total_raw_points + $d_total_raw_points + $vd_total_raw_points;           

            $vs_grand_total_raw_points += $vs_total_raw_points;
            $s_grand_total_raw_points += $s_total_raw_points;
            $ndvd_grand_total_raw_points += $n_total_raw_points + $d_total_raw_points + $vd_total_raw_points;
            $grand_total_raw_points += $total_raw_points;

            $trp_totals[$dimensionId] = [
                'vs_total_raw_points' => $vs_total_raw_points,
                's_total_raw_points' => $s_total_raw_points,
                'n_total_raw_points' => $n_total_raw_points,
                'd_total_raw_points' => $d_total_raw_points,
                'vd_total_raw_points' => $vd_total_raw_points,
                'total_raw_points' => $total_raw_points,
            ];

            //Part 1 Quarter 1 total scores
            $x_vs_total = $vs_total_raw_points * 5; 
            $x_s_total = $s_total_raw_points * 4; 
            $x_n_total = $n_total_raw_points * 3; 
            $x_d_total = $d_total_raw_points * 3; 
            $x_vd_total = $vd_total_raw_points * 1; 
            $x_total_score = $x_vs_total + $x_s_total + $x_n_total + $x_d_total + $x_vd_total;
            
            $vs_grand_total_score += $x_vs_total;
            $s_grand_total_score += $x_s_total;
            $ndvd_grand_total_score += $x_n_total + $x_d_total + $x_vd_total;
            $grand_total_score += $x_total_score;

            $p1_total_scores[$dimensionId] = [ 
                'x_vs_total' => $x_vs_total,
                'x_s_total' => $x_s_total,
                'x_n_total' => $x_n_total,
                'x_d_total' => $x_d_total,
                'x_vd_total' => $x_vd_total, 
                'x_total_score' => $x_total_score,
            ];

            // Likert Scale Rating = total score / grand total of total raw points
            if($total_raw_points != 0) {
                $vs_lsr_total = number_format(($x_vs_total / $total_raw_points), 2);
                $s_lsr_total = number_format(($x_s_total / $total_raw_points), 2);
                $n_lsr_total = number_format(($x_n_total / $total_raw_points), 2);
                $d_lsr_total = number_format(($x_d_total / $total_raw_points), 2);
                $vd_lsr_total = number_format(($x_vd_total / $total_raw_points), 2);
                $lsr_total = number_format(($vs_lsr_total + $s_lsr_total + $n_lsr_total + $d_lsr_total + $vd_lsr_total), 2);
                $lsr_grand_total += $lsr_total;
                $lsr_grand_total = number_format(($lsr_grand_total), 2);
                $lsr_average = number_format(($lsr_grand_total / $dimensionId), 2);
            }
            else {
                $vs_lsr_total = 0;
                $s_lsr_total = 0;
                $n_lsr_total = 0;
                $d_lsr_total = 0;
                $vd_lsr_total = 0;
                $lsr_total = 0;
                $lsr_grand_total += 0;
                $lsr_average = 0;
            }

            $lsr_totals[$dimensionId] = [
                'vs_lsr_total' => $vs_lsr_total,
                's_lsr_total' => $s_lsr_total,
                'n_lsr_total' => $n_lsr_total,
                'd_lsr_total' => $d_lsr_total,
                'vd_lsr_total' => $vd_lsr_total,
                'lsr_total' => $lsr_total,
            ];
            
            // PART II
            // Quarter 1 total responses with its dimensions and rate score
            $q1_vi_total = $q1_date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_i_total = $q1_date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_mi_total = $q1_date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_si_total = $q1_date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q1_nai_total = $q1_date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q1_i_grand_total = $q1_vi_total + $q1_i_total + $q1_mi_total + $q1_si_total + $q1_nai_total; 

            // Quarter 2 total responses with its dimensions and rate score
            $q2_vi_total = $q2_date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_i_total = $q2_date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_mi_total = $q2_date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_si_total = $q2_date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q2_nai_total = $q2_date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q2_i_grand_total = $q2_vi_total + $q2_i_total + $q2_mi_total + $q2_si_total + $q2_nai_total; 

            //  Quarter 3 total responses with its dimensions and rate score
            $q3_vi_total = $q3_date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_i_total = $q3_date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_mi_total = $q3_date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_si_total = $q3_date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q3_nai_total = $q3_date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q3_i_grand_total = $q3_vi_total + $q3_i_total + $q3_mi_total + $q3_si_total + $q3_nai_total;
            
            //  Quarter 4 total responses with its dimensions and rate score
            $q4_vi_total = $q4_date_range->where('importance_rate_score', 5)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_i_total = $q4_date_range->where('importance_rate_score', 4)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_mi_total = $q4_date_range->where('importance_rate_score', 3)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_si_total = $q4_date_range->where('importance_rate_score', 2)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();
            $q4_nai_total = $q4_date_range->where('importance_rate_score', 1)->where('dimension_id', $dimensionId)->groupBy('customer_id')->count();          

            $q4_i_grand_total = $q4_vi_total + $q4_i_total + $q4_mi_total + $q4_si_total + $q4_nai_total;
                // Very Important total with specific dimention or attribute
                $vi_totals[$dimensionId] = [
                    'q1_vi_total' => $q1_vi_total,
                    'q2_vi_total' => $q2_vi_total,
                    'q3_vi_total' => $q3_vi_total,
                    'q4_vi_total' => $q4_vi_total,
                ];

                //Important total with specific dimention or attribute
                $i_totals[$dimensionId] = [
                    'q1_i_total' => $q1_i_total,
                    'q2_i_total' => $q2_i_total,
                    'q3_i_total' => $q3_i_total,
                    'q4_i_total' => $q4_i_total,
                ];
                // Moderately Important total with specific dimention or attribute
                $mi_totals[$dimensionId] = [
                    'q1_mi_total' => $q1_mi_total,
                    'q2_mi_total' => $q2_mi_total,
                    'q3_mi_total' => $q3_mi_total,
                    'q4_mi_total' => $q4_mi_total,
                ];
                // slightly Important total with specific dimention or attribute
                $si_totals[$dimensionId] = [
                    'q1_si_total' => $q1_si_total,
                    'q2_si_total' => $q2_si_total,
                    'q3_si_total' => $q3_si_total,
                    'q4_si_total' => $q4_si_total,
                ];

                $nai_totals[$dimensionId] = [
                    'q1_nai_total' => $q1_nai_total,
                    'q2_nai_total' => $q2_nai_total,
                    'q3_nai_total' => $q3_nai_total,
                    'q4_nai_total' => $q4_nai_total,
                ];

                $i_grand_totals[$dimensionId] = [
                    'q1_i_grand_total' => $q1_i_grand_total,
                    'q2_i_grand_total' => $q2_i_grand_total,
                    'q3_i_grand_total' => $q3_i_grand_total,
                    'q4_i_grand_total' => $q4_i_grand_total,
                ];

                
            //Importance Total Raw Points totals
            $vi_total_raw_points = $q1_vi_total + $q2_vi_total + $q3_vi_total + $q4_vi_total;
            $i_total_raw_points = $q1_i_total + $q2_i_total + $q3_i_total + $q4_i_total;
            $mi_total_raw_points = $q1_mi_total + $q2_mi_total + $q3_mi_total + $q4_mi_total;
            $si_total_raw_points = $q1_si_total + $q2_si_total + $q3_si_total + $q4_si_total;
            $nai_total_raw_points = $q1_nai_total + $q2_nai_total + $q3_nai_total + $q4_nai_total;
            $importance_total_raw_points = $vi_total_raw_points + $i_total_raw_points + $mi_total_raw_points + $si_total_raw_points + $nai_total_raw_points;           

            $vi_grand_total_raw_points += $vi_total_raw_points;
            $s_grand_total_raw_points += $i_total_raw_points;
            $misinai_grand_total_raw_points += $mi_total_raw_points + $si_total_raw_points + $nai_total_raw_points;
            $i_grand_total_raw_points += $total_raw_points;

            $i_trp_totals[$dimensionId] = [
                'vi_total_raw_points' => $vi_total_raw_points,
                'i_total_raw_points' => $i_total_raw_points,
                'mi_total_raw_points' => $mi_total_raw_points,
                'si_total_raw_points' => $si_total_raw_points,
                'nai_total_raw_points' => $nai_total_raw_points,
                'importance_total_raw_points' => $importance_total_raw_points,
            ];

            //Part 1 Quarter 1 total scores
            $x_vi_total = $vi_total_raw_points * 5; 
            $x_i_total = $i_total_raw_points * 4; 
            $x_mi_total = $mi_total_raw_points * 3; 
            $x_si_total = $si_total_raw_points * 2; // Fixed: should be 2 instead of 3
            $x_nai_total = $nai_total_raw_points * 1; 
            $x_i_total_score = $x_vi_total + $x_i_total + $x_mi_total + $x_si_total + $x_nai_total;
            $overall_total_scores += $x_i_total_score;
            
            $vi_grand_total_score += $x_vi_total;
            $i_grand_total_score += $x_i_total; // Fixed: should be x_i_total instead of x_si_total
            $misinai_grand_total_score += $x_mi_total + $x_si_total + $x_nai_total;

            $i_total_scores[$dimensionId] = [ 
                'x_vi_total' => $x_vi_total,
                'x_i_total' => $x_i_total,
                'x_mi_total' => $x_mi_total,
                'x_si_total' => $x_si_total,
                'x_nai_total' => $x_nai_total, 
                'x_i_total_score' => $x_i_total_score,
            ];
        }


        //ALL quarters Total No. of Very Satisfied (VS) Responses of First Quarte
        $q1_total_vs_respondents = $q1_date_range->where('rate_score', 5)->groupBy('customer_id')->count();
        $q2_total_vs_respondents = $q2_date_range->where('rate_score', 5)->groupBy('customer_id')->count();
        $q3_total_vs_respondents = $q3_date_range->where('rate_score', 5)->groupBy('customer_id')->count();
        $q4_total_vs_respondents = $q4_date_range->where('rate_score', 5)->groupBy('customer_id')->count();

        // Total No. of Satisfied (S) Responses
        $q1_total_s_respondents = $q1_date_range->where('rate_score', 4)->groupBy('customer_id')->count();
        $q2_total_s_respondents = $q2_date_range->where('rate_score', 4)->groupBy('customer_id')->count();
        $q3_total_s_respondents = $q3_date_range->where('rate_score', 4)->groupBy('customer_id')->count();
        $q4_total_s_respondents = $q4_date_range->where('rate_score', 4)->groupBy('customer_id')->count();

        // Total No. of Other (N, D, VD) Responses
        $q1_total_ndvd_respondents = $q1_date_range->where('rate_score', '<', 4)->groupBy('customer_id')->count();
        $q2_total_ndvd_respondents = $q2_date_range->where('rate_score', '<', 4)->groupBy('customer_id')->count();
        $q3_total_ndvd_respondents = $q3_date_range->where('rate_score', '<', 4)->groupBy('customer_id')->count();
        $q4_total_ndvd_respondents = $q4_date_range->where('rate_score', '<', 4)->groupBy('customer_id')->count();
        
        // Total No. of All Responses
        $q1_total_respondents = $q1_date_range->groupBy('customer_id')->count();
        $q2_total_respondents = $q2_date_range->groupBy('customer_id')->count();
        $q3_total_respondents = $q3_date_range->groupBy('customer_id')->count();
        $q4_total_respondents = $q4_date_range->groupBy('customer_id')->count();
        

        //Total respondents /Customers
        $total_respondents = $date_range->groupBy('customer_id')->count();
        $q1_total_respondents = $q1_date_range->groupBy('customer_id')->count();
        $q2_total_respondents = $q2_date_range->groupBy('customer_id')->count();
        $q3_total_respondents = $q3_date_range->groupBy('customer_id')->count();
        $q4_total_respondents = $q4_date_range->groupBy('customer_id')->count();

        // total number of respondents/customer who rated VS/S
        $total_vss_respondents = $date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();
        $q1_total_vss_respondents = $q1_date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();
        $q2_total_vss_respondents = $q2_date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();
        $q3_total_vss_respondents = $q3_date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();
        $q4_total_vss_respondents = $q4_date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();

        // total number of respondents/customer who rated VS
        $total_vs_respondents = $date_range->where('rate_score', '=', '5')->groupBy('customer_id')->count();
        $q1_total_vs_respondents = $q1_date_range->where('rate_score', '=', '5')->groupBy('customer_id')->count();
        $q2_total_vs_respondents = $q2_date_range->where('rate_score', '=', '5')->groupBy('customer_id')->count();
        $q3_total_vs_respondents = $q3_date_range->where('rate_score', '=', '5')->groupBy('customer_id')->count();
        $q4_total_vs_respondents = $q4_date_range->where('rate_score', '=', '5')->groupBy('customer_id')->count();

        // total number of respondents/customer who rated S
        $total_s_respondents = $date_range->where('rate_score', '=', '4')->groupBy('customer_id')->count();
        $q1_total_s_respondents = $q1_date_range->where('rate_score', '=', '4')->groupBy('customer_id')->count();
        $q2_total_s_respondents = $q2_date_range->where('rate_score', '=', '4')->groupBy('customer_id')->count();
        $q3_total_s_respondents = $q3_date_range->where('rate_score', '=', '4')->groupBy('customer_id')->count();
        $q4_total_s_respondents = $q4_date_range->where('rate_score', '=', '4')->groupBy('customer_id')->count();

        // Frst quarter total number of promoters or respondents who rated 7-10 in recommendation rating
        $total_promoters = $customer_recommendation_ratings->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        $q1_total_promoters = $q1_crr->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        $q2_total_promoters = $q2_crr->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        $q3_total_promoters = $q3_crr->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        $q4_total_promoters = $q4_crr->where('recommend_rate_score', '>', '6')->groupBy('customer_id')->count();
        
        // total number of detractors or respondents who rated 0-6 in recommendation rating
        $total_detractors = $customer_recommendation_ratings->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();
        $q1_total_detractors = $q1_crr->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();
        $q2_total_detractors = $q2_crr->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();
        $q3_total_detractors = $q3_crr->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();
        $q4_total_detractors = $q4_crr->where('recommend_rate_score', '<', '7')->groupBy('customer_id')->count();


        //Percentage of Respondents/Customers who rated VS/S = total no. of respondents / total no. respondets who rated vs/s * 100
        $percentage_vss_respondents = 0;
        if($total_respondents != 0 && $total_vss_respondents != 0) {
            $percentage_vss_respondents = ($total_vss_respondents / $total_respondents) * 100; // Fixed calculation
        }
        $percentage_vss_respondents = number_format($percentage_vss_respondents, 2);

        // CSAT = ((Total No. of Very Satisfied (VS) Responses + Total No. of Satisfied (S) Responses) / grand total respondents) * 100
        $customer_satisfaction_rating = 0;
        if($grand_total_score != 0) { // Fixed condition
            $customer_satisfaction_rating = (($vs_grand_total_score + $s_grand_total_score) / $grand_total_score) * 100;
        }
        $customer_satisfaction_rating = number_format($customer_satisfaction_rating, 2);

        //Percentage of Promoters = number of promoters / total respondents
        $percentage_promoters = 0;
        //  Percentage promoters
        $q1_percentage_promoters = 0.00;
        $q2_percentage_promoters = 0.00;
        $q3_percentage_promoters = 0.00;
        $q4_percentage_promoters = 0.00;

        // Percentage of promoter
        $q1_percentage_detractors = 0.00;
        $q2_percentage_detractors = 0.00;
        $q3_percentage_detractors = 0.00;
        $q4_percentage_detractors = 0.00;

        // Net Promoter Score
        $q1_net_promoter_score = 0.00;
        $q2_net_promoter_score = 0.00;
        $q3_net_promoter_score = 0.00;
        $q4_net_promoter_score = 0.00;

        // average
        $ave_net_promoter_score = 0.00;
        $average_percentage_promoters = 0.00;
        $average_percentage_detractors = 0.00;
        $percentage_detractors = 0.00; // Added missing declaration

        if($total_respondents != 0) {
            if($total_promoters > 0) {
                $percentage_promoters = number_format((($total_promoters / $total_respondents) * 100), 2);
            }
            
            // Quarter 1 percentages
            if($q1_total_respondents != 0) {
                if($q1_total_promoters > 0) {
                    $q1_percentage_promoters = number_format((($q1_total_promoters / $q1_total_respondents) * 100), 2);
                }
                if($q1_total_detractors > 0) {
                    $q1_percentage_detractors = number_format((($q1_total_detractors / $q1_total_respondents) * 100), 2);
                }
            }
            
            // Quarter 2 percentages
            if($q2_total_respondents != 0) {
                if($q2_total_promoters > 0) {
                    $q2_percentage_promoters = number_format((($q2_total_promoters / $q2_total_respondents) * 100), 2);
                }
                if($q2_total_detractors > 0) {
                    $q2_percentage_detractors = number_format((($q2_total_detractors / $q2_total_respondents) * 100), 2);
                }
            }
            
            // Quarter 3 percentages
            if($q3_total_respondents != 0) {
                if($q3_total_promoters > 0) {
                    $q3_percentage_promoters = number_format((($q3_total_promoters / $q3_total_respondents) * 100), 2);
                }
                if($q3_total_detractors > 0) {
                    $q3_percentage_detractors = number_format((($q3_total_detractors / $q3_total_respondents) * 100), 2);
                }
            }
            
            // Quarter 4 percentages
            if($q4_total_respondents != 0) {
                if($q4_total_promoters > 0) {
                    $q4_percentage_promoters = number_format((($q4_total_promoters / $q4_total_respondents) * 100), 2);
                }
                if($q4_total_detractors > 0) {
                    $q4_percentage_detractors = number_format((($q4_total_detractors / $q4_total_respondents) * 100), 2);
                }
            }
        
            //Percentage of Detractors = number of detractors / total respondents
            if($total_detractors > 0) {
                $percentage_detractors = number_format((($total_detractors / $total_respondents) * 100), 2);
            }
            
            // Net Promotion Scores(NPS) = Percentage of Promoters−Percentage of Detractors
            $q1_net_promoter_score = number_format(($q1_percentage_promoters - $q1_percentage_detractors), 2);
            $q2_net_promoter_score = number_format(($q2_percentage_promoters - $q2_percentage_detractors), 2);
            $q3_net_promoter_score = number_format(($q3_percentage_promoters - $q3_percentage_detractors), 2);
            $q4_net_promoter_score = number_format(($q4_percentage_promoters - $q4_percentage_detractors), 2);

            $ave_net_promoter_score = number_format((($q1_net_promoter_score + $q2_net_promoter_score + $q3_net_promoter_score + $q4_net_promoter_score) / 4), 2);
            $average_percentage_promoters = number_format((($q1_percentage_promoters + $q2_percentage_promoters + $q3_percentage_promoters + $q4_percentage_promoters) / 4), 2);
            $average_percentage_detractors = number_format((($q1_percentage_detractors + $q2_percentage_detractors + $q3_percentage_detractors + $q4_percentage_detractors) / 4), 2);
        }


        // get Yearly CSI
        $jan_csi = $this->getMonthlyCSI($request, $office_id, 1);
        $feb_csi = $this->getMonthlyCSI($request, $office_id, 2);
        $mar_csi = $this->getMonthlyCSI($request, $office_id, 3);

        $q1_csi = 0;
        if($jan_csi > 0 || $feb_csi > 0 || $mar_csi > 0) {
            $q1_csi = number_format(($jan_csi + $feb_csi + $mar_csi) / 3, 2); // Calculated as average
        }

        $apr_csi = $this->getMonthlyCSI($request, $office_id, 4);
        $may_csi = $this->getMonthlyCSI($request, $office_id, 5);
        $jun_csi = $this->getMonthlyCSI($request, $office_id, 6);
        
        $q2_csi = 0;
        if($apr_csi > 0 || $may_csi > 0 || $jun_csi > 0) {
            $q2_csi = number_format(($apr_csi + $may_csi + $jun_csi) / 3, 2); // Calculated as average
        }

        $jul_csi = $this->getMonthlyCSI($request, $office_id, 7);
        $aug_csi = $this->getMonthlyCSI($request, $office_id, 8);
        $sep_csi = $this->getMonthlyCSI($request, $office_id, 9);

        $q3_csi = 0;
        if($jul_csi > 0 || $aug_csi > 0 || $sep_csi > 0) {
            $q3_csi = number_format(($jul_csi + $aug_csi + $sep_csi) / 3, 2); // Calculated as average
        }

        $oct_csi = $this->getMonthlyCSI($request, $office_id, 10);
        $nov_csi = $this->getMonthlyCSI($request, $office_id, 11);
        $dec_csi = $this->getMonthlyCSI($request, $office_id, 12);

        $q4_csi = 0;
        if($oct_csi > 0 || $nov_csi > 0 || $dec_csi > 0) {
            $q4_csi = number_format(($oct_csi + $nov_csi + $dec_csi) / 3, 2); // Calculated as average
        }

        // Calculate yearly CSI as average of quarterly CSIs that have data
        $count_quarters = 0;
        $sum_quarters = 0;
        
        if ($q1_csi > 0) {
            $sum_quarters += $q1_csi;
            $count_quarters++;
        }
        if ($q2_csi > 0) {
            $sum_quarters += $q2_csi;
            $count_quarters++;
        }
        if ($q3_csi > 0) {
            $sum_quarters += $q3_csi;
            $count_quarters++;
        }
        if ($q4_csi > 0) {
            $sum_quarters += $q4_csi;
            $count_quarters++;
        }
        
        $customer_satisfaction_index = 0;
        if ($count_quarters > 0) {
            $customer_satisfaction_index = number_format($sum_quarters / $count_quarters, 2);
        }
        
        if($customer_satisfaction_index > 100) {
            $customer_satisfaction_index = number_format(100, 2);
        }

        // comments and complaints
        $comment_list = CustomerComment::whereIn('customer_id', $customer_ids)
                                        ->whereYear('created_at', $request->selected_year)->get();

        $comments = $comment_list->where('comment', '!=', '')->pluck('comment'); 

        $total_comments = $comment_list->where('comment', '!=', '')->count();
        $total_complaints = $comment_list->where('is_complaint', 1)->count();

        // Respondents list
        $data = CARResource::collection($respondents_list);

        // Get service data
        $service = $this->getServiceById($service_id);
        
        // Get sub-service data if it exists
        $sub_service = null;
        if ($sub_service_id) {
            $sub_service = $this->getSubServiceById($sub_service_id);
        }

        // send response to front end
        return Inertia::render('CSI/Index')
            ->with('user', $user)
            ->with('cc_data', $cc_data)
            ->with('assignatorees', $assignatorees)
            ->with('services', $services)
            ->with('sub_services', $sub_services)
            ->with('dimensions', $dimensions)
            ->with('division', $request->division)
            ->with('section', null) // No section since we're generating by service
            ->with('service', $service)
            ->with('sub_service', $sub_service) // Added sub-service to view
            ->with('respondents_list', $data)
            ->with('vs_totals', $vs_totals)
            ->with('s_totals', $s_totals)
            ->with('n_totals', $n_totals)
            ->with('d_totals', $d_totals)
            ->with('vd_totals', $vd_totals)
            ->with('grand_totals', $grand_totals)
            ->with('trp_totals', $trp_totals)
            ->with('grand_total_raw_points', $grand_total_raw_points)
            ->with('vs_grand_total_raw_points', $vs_grand_total_raw_points)
            ->with('s_grand_total_raw_points', $s_grand_total_raw_points)
            ->with('ndvd_grand_total_raw_points', $ndvd_grand_total_raw_points)
            ->with('p1_total_scores', $p1_total_scores)
            ->with('vs_grand_total_score', $vs_grand_total_score) 
            ->with('s_grand_total_score', $s_grand_total_score) 
            ->with('ndvd_grand_total_score', $ndvd_grand_total_score) 
            ->with('grand_total_score', $grand_total_score) 
            ->with('lsr_totals', $lsr_totals)
            ->with('lsr_grand_total', $lsr_grand_total)
            ->with('lsr_average', $lsr_average) 
            ->with('q1_total_vs_respondents', $q1_total_vs_respondents)
            ->with('q2_total_vs_respondents', $q2_total_vs_respondents)
            ->with('q3_total_vs_respondents', $q3_total_vs_respondents)
            ->with('q4_total_vs_respondents', $q4_total_vs_respondents)
            ->with('q1_total_s_respondents', $q1_total_s_respondents)
            ->with('q2_total_s_respondents', $q2_total_s_respondents)
            ->with('q3_total_s_respondents', $q3_total_s_respondents)
            ->with('q4_total_s_respondents', $q4_total_s_respondents)
            ->with('q1_total_ndvd_respondents', $q1_total_ndvd_respondents)
            ->with('q2_total_ndvd_respondents', $q2_total_ndvd_respondents)
            ->with('q3_total_ndvd_respondents', $q3_total_ndvd_respondents)
            ->with('q4_total_ndvd_respondents', $q4_total_ndvd_respondents)
            ->with('q1_total_respondents', $q1_total_respondents)
            ->with('q2_total_respondents', $q2_total_respondents)
            ->with('q3_total_respondents', $q3_total_respondents)
            ->with('q4_total_respondents', $q4_total_respondents)
            ->with('total_respondents', $total_respondents)
            ->with('q1_total_vss_respondents', $q1_total_vss_respondents)
            ->with('q2_total_vss_respondents', $q2_total_vss_respondents)
            ->with('q3_total_vss_respondents', $q3_total_vss_respondents)
            ->with('q4_total_vss_respondents', $q4_total_vss_respondents)
            ->with('total_vss_respondents', $total_vss_respondents)
            ->with('percentage_vss_respondents', $percentage_vss_respondents)
            ->with('total_promoters', $total_promoters)
            ->with('total_detractors', $total_detractors)
            ->with('vi_totals', $vi_totals)
            ->with('i_totals', $i_totals)
            ->with('mi_totals', $mi_totals)
            ->with('si_totals', $si_totals)
            ->with('nai_totals', $nai_totals)
            ->with('i_grand_totals', $i_grand_totals)
            ->with('i_trp_totals', $i_trp_totals)
            ->with('i_grand_total_raw_points', $i_grand_total_raw_points)
            ->with('vi_grand_total_raw_points', $vi_grand_total_raw_points)
            ->with('s_grand_total_raw_points', $s_grand_total_raw_points)
            ->with('misinai_grand_total_raw_points', $misinai_grand_total_raw_points)
            ->with('i_total_scores', $i_total_scores)
            ->with('vi_grand_total_score', $vi_grand_total_score) 
            ->with('i_grand_total_score', $i_grand_total_score) 
            ->with('misinai_grand_total_score', $misinai_grand_total_score)
            ->with('percentage_promoters', $percentage_promoters)
            ->with('q1_percentage_promoters', $q1_percentage_promoters)
            ->with('q2_percentage_promoters', $q2_percentage_promoters)
            ->with('q3_percentage_promoters', $q3_percentage_promoters)
            ->with('q4_percentage_promoters', $q4_percentage_promoters)
            ->with('average_percentage_promoters', $average_percentage_promoters)
            ->with('q1_percentage_detractors', $q1_percentage_detractors)
            ->with('q2_percentage_detractors', $q2_percentage_detractors)
            ->with('q3_percentage_detractors', $q3_percentage_detractors) 
            ->with('q4_percentage_detractors', $q4_percentage_detractors) 
            ->with('average_percentage_detractors', $average_percentage_detractors)
            ->with('q1_net_promoter_score', $q1_net_promoter_score)
            ->with('q2_net_promoter_score', $q2_net_promoter_score)
            ->with('q3_net_promoter_score', $q3_net_promoter_score)
            ->with('q4_net_promoter_score', $q4_net_promoter_score)
            ->with('ave_net_promoter_score', $ave_net_promoter_score)
            ->with('customer_satisfaction_rating', $customer_satisfaction_rating)
            ->with('q1_csi', $q1_csi)
            ->with('q2_csi', $q2_csi)
            ->with('q3_csi', $q3_csi)
            ->with('q4_csi', $q4_csi)
            ->with('csi', $customer_satisfaction_index)
            ->with('total_comments', $total_comments)
            ->with('total_complaints', $total_complaints)
            ->with('comments', $comments)
            ->with('request', $request);
    }

    public function getMonthlyCSI($request, $office_id, $month)
    {
        $division_id = $request->division['id'];
        $service_id = $request->service_id; // Changed from section_id
        $sub_service_id = $request->sub_service_id; // Changed from sub_section_id
        $client_type = $request->client_type; 

        // search and check list of forms query
        // Pass null for section_id since we're using service-based approach
        $customer_ids = $this->querySearchCSF($office_id, $division_id, null, $service_id, $client_type, $sub_service_id);

        // Citizen's Charter
        $cc_query = CustomerCCRating::whereMonth('created_at', $month)
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

        //calculate CC
        $cc_data = $this->calculateCC($cc_query);

        $date_range = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                                            ->whereMonth('created_at', $month)->get();
        
        // Dimensions or attributes
        $dimensions = Dimension::all();
        $dimension_count = $dimensions->count();

        // total number of respondents/customer
        $total_respondents = $date_range->groupBy('customer_id')->count();

        // total number of respondents/customer who rated VS/S
        $total_vss_respondents = $date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();

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
            if($x_importance_total != 0 && $total_respondents > 0) {
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
            //PART I
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
            $x_respondents_total = $vs_total + $s_total + $n_total + $d_total + $vd_total; // Fixed calculation
            $x_grand_total = $x_vs_total + $x_s_total + $x_n_total + $x_d_total + $x_vd_total; 
        
            // right side total score divided by total repondents or customers
            if($x_grand_total != 0) {
                if($dimensionId == 6) {
                    $lsr_total = $x_respondents_total > 0 ? $x_grand_total / $x_respondents_total : 0;
                }
                else {
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
                    
            // PART II
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
            if($x_importance_total != 0 && $total_respondents > 0) {
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
            if($ilsr_total != 0 && $ilsr_grand_total != 0) {
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

        // round off Likert Scale Rating grand total and control decimal to 2 
        $lsr_grand_total = number_format($lsr_grand_total, 2);      

        // table below total score
        $grand_vs_total = $grand_vs_total * 5;
        $grand_s_total = $grand_s_total * 4; // Fixed: should be 4 instead of 5
        $grand_n_total = $grand_n_total * 3; // Fixed: should be 3 instead of 5
        $grand_d_total = $grand_d_total * 2; // Fixed: should be 2 instead of 5
        $grand_vd_total = $grand_vd_total * 1; // Fixed: should be 1 instead of 5

        $x_grand_total = $grand_vs_total + $grand_s_total + $grand_n_total + $grand_d_total + $grand_vd_total;

        //Percentage of Respondents/Customers who rated VS/S: 
        // = total no. of respondents who rated vs/s / total no. respondets * 100
        $percentage_vss_respondents = 0;
        if($total_respondents != 0 && $total_vss_respondents != 0) {
            $percentage_vss_respondents = ($total_vss_respondents / $total_respondents) * 100; // Fixed calculation
        }
        $percentage_vss_respondents = number_format($percentage_vss_respondents, 2);

        $customer_satisfaction_rating = 0;
        if($x_grand_total > 0) {
            $customer_satisfaction_rating = (($grand_vs_total + $grand_s_total) / $x_grand_total) * 100; // Fixed calculation
        }
        $customer_satisfaction_rating = number_format($customer_satisfaction_rating, 2);

        // Customer Satisfaction Index (CSI) = (ws grand total / 5) * 100
        $customer_satisfaction_index = 0;
        if($ws_grand_total != 0) {
            $customer_satisfaction_index = ($ws_grand_total / 5) * 100;
        }
        $customer_satisfaction_index = number_format($customer_satisfaction_index, 2);
        
        if($customer_satisfaction_index > 100) {
            $customer_satisfaction_index = number_format(100, 2);
        }

        return $customer_satisfaction_index;
    }
    
    public function generateCSIAllServiceMonthly($request)
    {
        //get user
        $user = Auth::user();

        $numeric_month = Carbon::parse("1 {$request->selected_month}")->format('m');

        //PART I: Citizens Charter
        $cc_query = CustomerCCRating::whereMonth('created_at', $numeric_month)
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
                                    });
        $cc_data = $this->calculateCC($cc_query);

        // PART II:
        // --dimensions
        // --divisions and services
        // --totals

        $dimensions = Dimension::all();
        $dimension_count = $dimensions->count();

        $divisions = Division::all();
        //all Divisions and their services
        $division_services = DivisionServicesResource::collection($divisions);

        //OFFICE of the REGIONAL DIRECTOR
        // Service 1: SECRETARIAT
        //---total number of respondents
        $secretariat_total_respo = CsfForm::where('office_id', $user->office_id)
                ->where('division_id', 1)
                ->where('service_id', 1) // Changed from section_id to service_id
                ->count();
        
        // Get customer ids to fetch all customers for the specific service
        $customer_ids = CsfForm::where('office_id', $user->office_id)
                        ->where('division_id', 1)
                        ->where('service_id', 1) // Changed from section_id to service_id
                        ->pluck('customer_id');

        // Total number of respondents who rated 5 or 4 (Very Satisfied or Satisfied)
        $secretariat_total_vss_respo = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                    ->whereIn('rate_score', [4, 5]) // Check for rate_score 4 or 5
                    ->distinct('customer_id') // Ensure unique customer_id counts
                    ->count('customer_id'); // Count distinct customer_id values
        
        // Percentage of respondents who rated 5 or 4 (Very Satisfied or Satisfied)
        $secretariat_percentage_vss_respo = 0;
        if($secretariat_total_respo != 0 && $secretariat_total_vss_respo != 0) {
            $secretariat_percentage_vss_respo = ($secretariat_total_vss_respo / $secretariat_total_respo) * 100; // Fixed calculation
        }

        //get monthly CSI
        $monthly_csi = $this->getAllServiceMonthlyCSI($request, $user->office_id, $numeric_month);
    
        // Service 2: Gender and Development(GAD)
        //---total number of respondents
        $gad_total_respo = CsfForm::where('office_id', $user->office_id)
                ->where('division_id', '1')
                ->where('service_id', 2) // Changed from section_id to service_id
                ->count();

        // Get customer ids to fetch all customers for the specific service
        $customer_ids = CsfForm::where('office_id', $user->office_id)
                ->where('division_id', 1)
                ->where('service_id', 2) // Changed from section_id to service_id
                ->pluck('customer_id');

        // Total number of respondents who rated 5 or 4 (Very Satisfied or Satisfied)
        $gad_total_vss_respo = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                ->whereIn('rate_score', [4, 5]) // Check for rate_score 4 or 5
                ->distinct('customer_id') // Ensure unique customer_id counts
                ->count('customer_id'); // Count distinct customer_id values
        
        // Percentage of respondents who rated 5 or 4 (Very Satisfied or Satisfied)
        $gad_percentage_vss_respo = 0;
        if($gad_total_respo != 0 && $gad_total_vss_respo != 0) {
            $gad_percentage_vss_respo = ($gad_total_vss_respo / $gad_total_respo) * 100; // Fixed calculation
        }

        // Service 3: Fairness Opinion Board
        //---total number of respondents
        $fairness_total_respo = CsfForm::where('office_id', $user->office_id)
                ->where('division_id', '1')
                ->where('service_id', 3) // Changed from section_id to service_id
                ->count();
        
        // Get customer ids to fetch all customers for the specific service
        $customer_ids = CsfForm::where('office_id', $user->office_id)
                ->where('division_id', 1)
                ->where('service_id', 3) // Changed from section_id to service_id
                ->pluck('customer_id');

        // Total number of respondents who rated 5 or 4 (Very Satisfied or Satisfied)
        $fairness_total_vss_respo = CustomerAttributeRating::whereIn('customer_id', $customer_ids)
                ->whereIn('rate_score', [4, 5]) // Check for rate_score 4 or 5
                ->distinct('customer_id') // Ensure unique customer_id counts
                ->count('customer_id'); // Count distinct customer_id values
            
        // Percentage of respondents who rated 5 or 4 (Very Satisfied or Satisfied)
        $fairness_percentage_vss_respo = 0;
        if($fairness_total_respo != 0 && $fairness_total_vss_respo != 0) {
            $fairness_percentage_vss_respo = ($fairness_total_vss_respo / $fairness_total_respo) * 100; // Fixed calculation
        }
        
        $ord_total_respondents = [
            'secretariat_total_respo' => $secretariat_total_respo,
            'gad_total_respo' => $gad_total_respo,
            'fairness_total_respo' => $fairness_total_respo,
        ];

        $ord_total_vss_respondents = [
            'secretariat_total_vss_respo' => $secretariat_total_vss_respo,
            'gad_total_vss_respo' => $gad_total_vss_respo,
            'fairness_total_vss_respo' => $fairness_total_vss_respo,
        ];

        $ord_percentage_vss_respondents = [
            'secretariat_percentage_vss_respo' => $secretariat_percentage_vss_respo,
            'gad_percentage_vss_respo' => $gad_percentage_vss_respo,
            'fairness_percentage_vss_respo' => $fairness_percentage_vss_respo,
        ];

        //send response to front end
        return Inertia::render('CSI/Index') 
                    ->with('division_services', $division_services) 
                    ->with('cc_data', $cc_data)
                    ->with('ord_total_respondents', $ord_total_respondents)
                    ->with('ord_total_vss_respondents', $ord_total_vss_respondents)
                    ->with('ord_percentage_vss_respondents', $ord_percentage_vss_respondents)
                    ->with('csi_total', $monthly_csi);
    }

    public function getAllServiceMonthlyCSI($request, $office_id, $numeric_month)
    {        
        // Dimensions or attributes
        $dimensions = Dimension::all();
        $dimension_count = $dimensions->count();

        $date_range = CustomerAttributeRating::whereMonth('created_at', $numeric_month)
                                            ->whereYear('created_at', $request->selected_year);

        // total number of respondents/customer
        $total_respondents = $date_range->count();

        // total number of respondents/customer who rated VS/S
        $total_vss_respondents = $date_range->where('rate_score', '>', '3')->groupBy('customer_id')->count();

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
            if($x_importance_total != 0 && $total_respondents > 0) {
                $ilsr_total = $x_importance_total / $total_respondents;
                $ilsr_grand_total = $ilsr_grand_total + $ilsr_total;
            }
        }

        // PART I : CUSTOMER RATING OF SERVICE QUALITY 

        //set initial value of bottom side total scores
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
            //PART I
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

            // sum of all respondent with rate_score 1-5
            $x_respondents_total = $vs_total + $s_total + $n_total + $d_total + $vd_total; // Fixed calculation
            $x_grand_total = $x_vs_total + $x_s_total + $x_n_total + $x_d_total + $x_vd_total; 
        
            // right side total score divided by total respondents or customers
            if($x_grand_total != 0) {
                if($dimensionId == 6) {
                    $lsr_total = $x_respondents_total > 0 ? $x_grand_total / $x_respondents_total : 0;
                }
                else {
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
                    
            // PART II
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
            if($x_importance_total != 0 && $total_respondents > 0) {
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
            if($ilsr_total != 0 && $ilsr_grand_total != 0) {
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

        // Customer Satisfaction Index (CSI) = (ws grand total / 5) * 100
        $customer_satisfaction_index = 0;
        if($ws_grand_total != 0) {
            $customer_satisfaction_index = ($ws_grand_total / 5) * 100;
        }
        $customer_satisfaction_index = number_format($customer_satisfaction_index, 2);
        
        if($customer_satisfaction_index > 100) {
            $customer_satisfaction_index = number_format(100, 2);
        }

        return $customer_satisfaction_index;
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

        return Inertia::render('CSI/AllServicesUnits/Index')
            ->with('division_sections', $data);
    }

    public function generateAllSectionReports(Request $request)
    {
        //dd($request->all());
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
                return $this->generateCSIAllThirdQuarter($request);
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