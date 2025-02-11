<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Section;
use App\Models\Division;
use App\Models\Office;
use App\Models\CSFForm;
use App\Models\SubSection;
use App\Models\Customer;
use App\Models\Dimension;
use App\Models\CcQuestion;
use Illuminate\Http\Request;
use App\Models\CustomerComment;
use App\Models\CustomerCCRating;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\DB;
use App\Models\SubSectionType;
use App\Models\ShowDateCsfForm;
use App\Models\CustomerAttributeRating;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\SurveyFormRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerRecommendationRating;
use App\Models\CustomerOtherAttributeIndication;

use App\Http\Resources\Section as SectionResource;
use App\Http\Resources\SubSection as SubSectionResource;
use App\Http\Resources\SubSectionType as SubSectionTypeResource;
use App\Http\Resources\ShowDateCSFForm as ShowDateCSFFormResource;

use App\Models\CustomerSignature;

class SurveyFormController extends Controller
{
    public function index(Request $request)
    {
        // get the data if the date will be displayed or not
        $date_display = ShowDateCsfForm::all();

        $cc_questions = CcQuestion::all();
        $dimensions = Dimension::all();
        $division = Division::where('id', $request->division_id)->get();
        $section = Section::where('id', $request->section_id)->get();
        $sub_section = SubSection::where('section_id', $request->section_id)
                           ->where('id', $request->sub_section_id)->get();
        
        $section = SectionResource::collection($section);
        $sub_section = SubSectionResource::collection($sub_section);

        return Inertia::render('Survey-Forms/Index')
            ->with('cc_questions', $cc_questions)
            ->with('dimensions', $dimensions)
            ->with('division', $division)
            ->with('section', $section)
            ->with('sub_section', $sub_section)
            ->with('date_display', $date_display);  
    }



    // SurveyFormRequest
    public function store(SurveyFormRequest $request)
    {       
        try{
            DB::beginTransaction();    
           
            //Save Customer
            $customer = $this->saveCustomer($request);

            // Validate dimension_form data
            $dimensionData = request()->validate([
                'dimension_form.id.*' => ['required', 'exists:dimensions,id'],
                'dimension_form.rate_score.*' => ['required', 'max:1'],
                'dimension_form.importance_rate_score.*' => ['required', 'max:1'],
            ]);
    
            // Associate ratings with dimensions for the customer
            foreach ($dimensionData['dimension_form']['id'] as $index => $dimensionId) {
                CustomerAttributeRating::create([
                    'created_at' =>  $request->date,
                    'updated_at' =>  $request->date,
                    'customer_id' => $customer->id,
                    'dimension_id' => $dimensionId,
                    'rate_score' => $dimensionData['dimension_form']['rate_score'][$index],
                    'importance_rate_score' => $dimensionData['dimension_form']['importance_rate_score'][$index],
                ]);
            }
    
            // Validate CC
            $ccData = request()->validate([
                'cc_form.id.*' => ['required', 'exists:cc_questions,id'],
                'cc_form.answer.*' => ['required', 'max:1'],
            ]);
    
            // Associate ratings with cc for the customer
            foreach ($ccData['cc_form']['id'] as $index => $ccId) {
                CustomerCCRating::create([
                    'created_at' =>  $request->date,
                    'updated_at' =>  $request->date,
                    'customer_id' => $customer->id,
                    'cc_id' => $ccId,
                    'answer' => $ccData['cc_form']['answer'][$index],
                ]);
            }
    
            // Save Comment
            if($request->comment){
                $this->saveComment($request, $customer);
            }
           
            // Save Customer Recommendation Rating
            $this->saveCustomerRecommendationRating($request, $customer);

            // SAve Other Attributes Indication
            // $this->saveCustomerOtherAttributeIndication($request, $customer);

            // Save csf form
            $this->saveCSFForm($request, $customer);

            DB::commit();
           
            return Inertia::render('Survey-Forms/ThankYou')
                ->with('message', "Successfully Submitted Thank you.")
                ->with('status', "success")
                ->with('current_url', $request->current_url);
            
            return Inertia::redirect('msg_index');

        } catch (\Exception $e) {
            DB::rollBack();
            //return $e;
            $msg = $e->getMessage();
            return back()->with([
                'message' => $msg ,
                'status' => "error",
            ]);

        }

        
   
    }

    public function saveCSFForm($request, $customer){
        $csf_form = new CSFForm();
        $csf_form->customer_id = $customer->id;
        $csf_form->office_id = $request->office_id;
        $csf_form->division_id = $request->division_id;
        $csf_form->section_id = $request->section_id;
        if($request->sub_section_id != "null"){
            $csf_form->sub_section_id = $request->sub_section_id;
        }
        $csf_form->client_type = $request->client_type;
        $csf_form->sub_section_type = $request->sub_section_type;
        if($request->date){
            $csf_form->created_at = $request->date;
            $csf_form->updated_at = $request->date;
        }
        
        $csf_form->save();

        return $csf_form;
    }

    public function saveCustomer($request){
        $customer = new Customer();
        $customer->email = $request->email;
        $customer->name = $request->name;
        $customer->client_type = $request->client_type;
        $customer->sex = $request->sex;
        $customer->age_group = $request->age_group;
        if($request->date){
            $customer->created_at = $request->date;
            $customer->updated_at = $request->date;
        }
         // 'signature_path' => $request->signature,
        $customer->save();

        return $customer;
    }

    public function saveComment($request, $customer){
         $comment = CustomerComment::create(
            [
                'created_at' => $request->date,
                'updated_at' => $request->date,
                'customer_id' => $customer->id,
                'comment' =>  $request->comment,
                'is_complaint' =>  $request->is_complaint,
            ]
         );
        return $comment;
    }

    public function saveCustomerRecommendationRating($request, $customer){
        $recommentdation_rating = CustomerRecommendationRating::create(
                [
                    'created_at' =>  $request->date,
                    'updated_at' =>  $request->date,
                    'customer_id' => $customer->id,
                    'recommend_rate_score' =>  $request->recommend_rate_score,      
                ]
            );
        return $recommentdation_rating;
    }

    public function saveCustomerOtherAttributeIndication($request, $customer){
        $customer_indication = CustomerOtherAttributeIndication::create(
                [
                    'created_at' =>  $request->date,
                    'updated_at' =>  $request->date,
                    'customer_id' => $customer->id,   
                    'indication' =>  $request->indication,           
                ]
            );
       return $customer_indication;
   }


   public function offices_index(Request $request){
        $offices = Office::all();
        return Inertia::render('Office')
                      ->with('offices', $offices );
   }

   public function divisions_index(Request $request){
        $divisions = Division::all();
        //selected office
        $office = Office::where('id',$request->office_id)->first();

        return Inertia::render('Divisions')
                        ->with('office_id', $request->office_id )
                        ->with('office', $office )
                        ->with('divisions', $divisions );
    }

    public function division_sections_index(Request $request){
       
        //selected office
        $office = Office::where('id',$request->office_id)->first();
        //selected division
        $division = Division::where('id', $request->division_id)->first();

        //check if division has sections
        $sections = Section::where('division_id', $request->division_id)->get();
       
      
        if(sizeof($sections) > 0){
            //if it has sections
            return Inertia::render('Sections')
                ->with('office_id', $request->office_id)
                ->with('office', $office)
                ->with('division_id', $request->division_id)
                ->with('division', $division)
                ->with('division_sections', $sections);
        }else{
            // else redirect to url of csf form
            $url = '/divisions/csf?office_id='.$request->office_id.
                                '&division_id='.$request->division_id;

            return Inertia::location($url);
        }
    }

    public function getSectionSubSections(Request $request){
        $sub_sections = SubSection::where('section_id', $request->section_id)->get();
        //selected office
        $office = Office::where('id',$request->office_id)->first();
        //selected section
        $section = Section::where('id', $request->section_id)->first();

        if(sizeof($sub_sections) > 0){
            return Inertia::render('SubSections')
                        ->with('office_id', $request->office_id)
                        ->with('office', $office)
                        ->with('division_id', $request->division_id)
                        ->with('section_id', $request->section_id)
                        ->with('section', $section)
                        ->with('sub_sections', $sub_sections);
        }else{
            // redirect to url of csf form

            $url = '/divisions/csf?office_id='.$request->office_id.
                                '&division_id='.$request->division_id.
                                '&section_id='.$request->section_id;

            return Inertia::location($url);
        }

       
    }



    

}
