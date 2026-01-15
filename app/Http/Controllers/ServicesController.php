<?php

namespace App\Http\Controllers;
use App\Models\Section;
use App\Models\Division;
use App\Models\Services;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Division as DivisionSectionsResource;
use App\Http\Resources\Services as ServicesResource;
use App\Http\Resources\SectionSubSection as SectionSubSectionResource;

class ServicesController extends Controller
{
    /**
     * READ - Show service or its subservices
     */
    public function index(Request $request, $service_id)
    {
        $service = Services::with('subServices')->findOrFail($service_id);

        // Just get the IDs from the request
        $office_id   = $request->office_id;
        $division_id = $request->division_id;
        $section_id  = $request->section_id; // may be null
        $sub_service_id = $request->sub_service_id; // optional

        // If service has subservices, still trigger survey form later
        if ($service->subServices->isNotEmpty()) {
            return Inertia::render('Survey/Form', [
                'service'        => $service,
                'office_id'      => $office_id,
                'division_id'    => $division_id,
                'section_id'     => $section_id,
                'sub_service_id' => $sub_service_id
            ]);
        }

        // No subservices → go directly to survey form
        return Inertia::render('Survey/Form', [
            'service'        => $service,
            'office_id'      => $office_id,
            'division_id'    => $division_id,
            'section_id'     => $section_id,
            'sub_service_id' => $sub_service_id
        ]);
    }


    /**
     * CREATE - Service
     */
    public function storeServices(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'service_availability' => 'nullable|string',
            'service_active' => 'nullable|boolean',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        Services::create([
            'service_name' => strtoupper($request->service_name),
            'service_description' => $request->service_description,
            'service_availability' => $request->service_availability,
            'service_active' => $request->has('service_active') ? $request->service_active : true,
            'division_id' => $request->division_id,
            'section_id' => $request->section_id,
        ]);

        return back()->with('message', 'Service added successfully.');
    }

    /**
     * UPDATE - Service
     */
    public function updateService(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'service_availability' => 'nullable|string',
            'service_active' => 'nullable|boolean',
            'division_id' => 'required|exists:divisions,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $service = Services::findOrFail($id);
        $service->update([
            'service_name' => strtoupper($request->service_name),
            'service_description' => $request->service_description,
            'service_availability' => $request->service_availability,
            'service_active' => $request->has('service_active') ? $request->service_active : true,
            'division_id' => $request->division_id,
            'section_id' => $request->section_id,
        ]);

        return back()->with('message', 'Service updated successfully.');
    }

    /**
     * DELETE - Service
     */
    public function destroyService($id)
    {
        $service = Services::findOrFail($id);
        $service->delete();

        return back()->with('message', 'Service deleted successfully.');
    }

    /**
     * CREATE - SubService
     */
    public function storeSubService(Request $request)
    {
        $request->validate([
            'parent_service_id' => 'required|exists:services,id',
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'service_availability' => 'nullable|string',
            'service_active' => 'nullable|boolean',
        ]);

        Services::create([
            'service_name' => strtoupper($request->service_name),
            'service_description' => $request->service_description,
            'service_availability' => $request->service_availability,
            'service_active' => $request->has('service_active') ? $request->service_active : true,
            'parent_service_id' => $request->parent_service_id,
        ]);

        return back()->with('message', 'SubService added successfully.');
    }

    /**
     * UPDATE - SubService
     */
    public function updateSubService(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'service_availability' => 'nullable|string',
            'service_active' => 'nullable|boolean',
            'parent_service_id' => 'required|exists:services,id',
        ]);

        $subService = Services::findOrFail($id);
        $subService->update([
            'service_name' => strtoupper($request->service_name),
            'service_description' => $request->service_description,
            'service_availability' => $request->service_availability,
            'service_active' => $request->has('service_active') ? $request->service_active : true,
            'parent_service_id' => $request->parent_service_id,
        ]);

        return back()->with('message', 'SubService updated successfully.');
    }

    /**
     * DELETE - SubService
     */
    public function destroySubService($id)
    {
        $subService = Services::findOrFail($id);
        $subService->delete();

        return back()->with('message', 'SubService deleted successfully.');
    }

    // public function divisionServicesIndex(Request $request)
    // {
    //     // Validate that division_id is provided
    //     $request->validate([
    //         'division_id' => 'required|exists:divisions,id'
    //     ]);

    //     $query = Services::query()
    //         ->where('division_id', $request->division_id)
    //         ->whereNull('section_id'); // Only services directly under the division

    //     if ($request->filled('office_id')) {
    //         $query->where('office_id', $request->office_id);
    //     }

    //     $services = $query->orderBy('service_name')->get();

    //     return response()->json([
    //         'services' => $services
    //     ]);
    // }

    // public function sectionServicesIndex(Request $request)
    // {
    //     // Validate that section_id is provided
    //     $request->validate([
    //         'section_id' => 'required|exists:sections,id'
    //     ]);

    //     $query = Services::query()
    //         ->where('section_id', $request->section_id); // Only services under this section

    //     if ($request->filled('office_id')) {
    //         $query->where('office_id', $request->office_id);
    //     }

    //     $services = $query->orderBy('service_name')->get();

    //     return response()->json([
    //         'services' => $services
    //     ]);
    // }

}