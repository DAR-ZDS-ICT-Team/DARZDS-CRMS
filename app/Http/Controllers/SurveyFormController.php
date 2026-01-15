<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Section;
use App\Models\Division;
use App\Models\Services;
use App\Models\Office;
use App\Models\CSFForm;
use App\Models\Customer;
use App\Models\Dimension;
use App\Models\CcQuestion;
use Illuminate\Http\Request;
use App\Models\CustomerComment;
use App\Models\CustomerCCRating;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\DB;
use App\Models\ShowDateCsfForm;
use App\Models\CustomerAttributeRating;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\SurveyFormRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\CustomerRecommendationRating;
use App\Models\CustomerOtherAttributeIndication;

use App\Http\Resources\Section as SectionResource;
use App\Http\Resources\Services as ServicesResource;
use App\Http\Resources\SubServices as SubServicesResource;

use App\Http\Resources\ShowDateCSFForm as ShowDateCSFFormResource;

use App\Models\CustomerSignature;
use App\Models\SubServices;

class SurveyFormController extends Controller
{
    public function index(Request $request)
    {
        // Validate required fields for safety
        $request->validate([
            'office_id'   => 'required|integer',
            'division_id' => 'required|integer',
            'service_id'  => 'required|integer',
            // section_id and sub_service_id are optional
        ]);

        // Global configurations
        $date_display = ShowDateCsfForm::all();
        $cc_questions = CcQuestion::all();
        $dimensions   = Dimension::all();

        // Required Models
        $office   = Office::findOrFail($request->office_id);
        $division = Division::findOrFail($request->division_id);
        $service  = new ServicesResource(
            Services::findOrFail($request->service_id)
        );

        // Optional Section
        $section = null;
        if ($request->filled('section_id') && $request->section_id !== 'undefined') {
            $section = new SectionResource(
                Section::findOrFail($request->section_id)
            );
        }

        // Optional Subservice
        $subService = null;
        if ($request->filled('sub_service_id') && $request->sub_service_id !== 'undefined') {
            $subService = new SubServicesResource(
                SubServices::findOrFail($request->sub_service_id)
            );
        }

        // Debug logging when APP_DEBUG = true
        if (config('app.debug')) {
            logger()->info('Survey form params', $request->only([
                'office_id',
                'division_id',
                'section_id',
                'service_id',
                'sub_services_id'
            ]));
        }

        return Inertia::render('Survey-Forms/Index', [
            'cc_questions' => $cc_questions,
            'dimensions'   => $dimensions,
            'office'       => $office,
            'division'     => $division,
            'section'      => $section,
            'service'      => $service,
            'sub_service'  => $subService,
            'date_display' => $date_display
        ]);
    }





    // SurveyFormRequest
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Save customer
            $customer = $this->saveCustomer($request);

            // Save CSF form (main survey record)
            $csf_form = $this->saveCSFForm($request, $customer);

            // Save comment (optional)
            if (!empty($request->comment)) {
                $this->saveComment($request, $customer);
            }

            // Save recommendation rating
            if (!is_null($request->recommend_rate_score)) {
                $this->saveCustomerRecommendationRating($request, $customer);
            }

            // Save "other" attribute indication (optional)
            if (!empty($request->indication)) {
                $this->saveCustomerOtherAttributeIndication($request, $customer);
            }

            DB::commit();

            return Inertia::render('Survey-Forms/ThankYou')
                ->with('message', "Successfully Submitted Thank you.")
                ->with('status', "success")
                ->with('current_url', $request->current_url);
            
            return Inertia::redirect('msg_index');


            // return response()->json([
            //     'message' => 'Survey form saved successfully',
            //     'data' => [
            //         'customer' => $customer,
            //         'csf_form' => $csf_form
            //     ]
            // ], 201);

        } 
        // catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json([
        //         'message' => 'Error saving survey form',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }

        catch (\Exception $e) {
            DB::rollBack();
            //return $e;
            $msg = $e->getMessage();
            return back()->with([
                'message' => $msg ,
                'status' => "error",
            ]);
        }
    }




    /**
     * Save a new customer record
     */
    public function saveCustomer(Request $request)
    {
        $customer = new Customer();
        $customer->email = $request->email;
        $customer->name = $request->name;
        $customer->client_type = $request->client_type;
        $customer->sex = $request->sex;
        $customer->age_group = $request->age_group;

        $this->setTimestamps($customer, $request->date);

        $customer->save();
        return $customer;
    }

    /**
     * Save the main CSF form entry
     */
    public function saveCSFForm(Request $request, Customer $customer)
    {
        $csf_form = new CSFForm();
        $csf_form->customer_id = $customer->id;
        $csf_form->office_id = $request->office_id;
        $csf_form->division_id = $request->division_id;
        $csf_form->section_id = $request->section_id ?: null;
        $csf_form->service_id = $request->service_id ?: null;
        $csf_form->sub_service_id = $request->sub_service_id ?: null;
        $csf_form->client_type = $request->client_type;

        $this->setTimestamps($csf_form, $request->date);

        $csf_form->save();
        return $csf_form;
    }

    /**
     * Save a customer comment
     */
    public function saveComment(Request $request, Customer $customer)
    {
        return CustomerComment::create([
            'customer_id' => $customer->id,
            'comment' => $request->comment,
            'is_complaint' => $request->is_complaint,
            'created_at' => $request->date,
            'updated_at' => $request->date,
        ]);
    }

    /**
     * Save recommendation rating
     */
    public function saveCustomerRecommendationRating(Request $request, Customer $customer)
    {
        return CustomerRecommendationRating::create([
            'customer_id' => $customer->id,
            'recommend_rate_score' => $request->recommend_rate_score,
            'created_at' => $request->date,
            'updated_at' => $request->date,
        ]);
    }

    /**
     * Save "other" attribute indication
     */
    public function saveCustomerOtherAttributeIndication(Request $request, Customer $customer)
    {
        return CustomerOtherAttributeIndication::create([
            'customer_id' => $customer->id,
            'indication' => $request->indication,
            'created_at' => $request->date,
            'updated_at' => $request->date,
        ]);
    }

    /**
     * Helper to set timestamps if a date is provided
     */
    private function setTimestamps($model, $date)
    {
        if ($date) {
            $model->created_at = $date;
            $model->updated_at = $date;
        }
    }



    /**
     * Step 1: List all offices
     */
    public function offices_index()
    {
        $offices = Office::all();

        return Inertia::render('Office', [
            'offices' => $offices
        ]);
    }

    /**
     * Step 2: List divisions for a selected office
     */
    public function divisions_index(Request $request)
    {
        $office = Office::findOrFail($request->office_id);
        $divisions = Division::where('office_id', $office->id)->get();

        return Inertia::render('Divisions', [
            'office_id'  => $office->id,
            'office'     => $office,
            'divisions'  => $divisions
        ]);
    }

    /**
     * Step 3: List sections for a selected division (if any), else go to services
     */
    public function division_sections_index(Request $request)
    {
        $office   = Office::findOrFail($request->office_id);
        $division = Division::findOrFail($request->division_id);

        $sections = Section::where('division_id', $division->id)
                   ->where('office_id', $office->id)
                   ->get();

        // If no sections, go directly to services
        $services = Services::where('division_id', $division->id)->get();


        if ($sections->isNotEmpty()) {
            return Inertia::render('Sections', [
                'office_id'         => $office->id,
                'office'            => $office,
                'division_id'       => $division->id,
                'division'          => $division,
                'sections'          => $sections,
                'services'          => $services
            ]);
        }

        

        if ($services->isNotEmpty()) {
            return Inertia::render('Services', [
                'office_id'   => $office->id,
                'office'      => $office,
                'division_id' => $division->id,
                'division'    => $division,
                'sections'   => $sections,
                'services'    => $services
            ]);
        }

        // If no sections or services, go directly to CSF
        return Inertia::location('/divisions/csf?' . http_build_query([
            'office_id'   => $office->id,
            'division_id' => $division->id
        ]));
    }

    /**
     * Step 4: List services for a selected section
     */
    public function section_services_index(Request $request)
    {
        // Basic sanity checks first
        if (!$request->filled('section_id') || $request->section_id === 'undefined') {
            Log::warning('section_services_index missing/undefined section_id', $request->all());
            return back()->with('error', 'Section is required.');
        }

        $office   = Office::findOrFail((int) $request->office_id);
        $division = Division::findOrFail((int) $request->division_id);

        $sectionId = (int) $request->section_id;
        $section   = Section::find($sectionId);

        if (!$section) {
            Log::warning('section_services_index: Section not found', ['section_id' => $sectionId] + $request->all());
            abort(404);
        }

        $services = Services::where('section_id', $section->id)->get();

        if ($services->isEmpty()) {
            // No services under this section → send user to CSFS
            return Inertia::location('/divisions/csf?' . http_build_query([
                'office_id'   => $office->id,
                'division_id' => $division->id,
                'section_id'  => $section->id,
            ]));
        }

        return Inertia::render('Services', [
            'office_id'   => $office->id,
            'office'      => $office,
            'division_id' => $division->id,
            'division'    => $division,
            'section_id'  => $section->id,
            'section'     => $section,
            'services'    => $services,
        ]);
    }


    /**
     * Step 5: Check if a service has subservices, else go to CSF form
     */
    public function checkServiceSubServices(Request $request)
    {
        $office   = Office::findOrFail($request->office_id);
        $division = Division::findOrFail($request->division_id);
        $section  = $request->section_id ? Section::find($request->section_id) : null;
        $service  = Services::findOrFail($request->service_id);

        $subServices = SubServices::where('service_id', $service->id)->get();

        if ($subServices->isEmpty()) {
            // No sub_services → send user to CSFS
            return Inertia::location('/divisions/csf?' . http_build_query([
                'office_id'   => $office->id,
                'division_id' => $division->id,
                'section_id'  => optional($section)->id,
                'service_id' => $service->id
            ]));
        }

        return Inertia::render('SubServices', [
            'office_id'     => $office->id,
            'office'        => $office,
            'division_id'   => $division->id,
            'division'      => $division,
            'section_id'    => optional($section)->id,
            'section'       => $section,
            'service_id'    => $service->id,
            'service'       => $service,
            'sub_services'  => $subServices
        ]);
    
    } 

}
