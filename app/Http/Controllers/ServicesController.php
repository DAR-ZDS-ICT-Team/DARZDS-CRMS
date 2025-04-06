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
    public function index()
    {
        $services = Services::all();

        $data = ServicesResource::collection($services);
        $user = Auth::user();

        return Inertia::render('Libraries/Services/Index')
            ->with('services', $data)
            ->with('user',  $user);
    }

    public function getDivisionServices(Request $request)
    {
        $division_services = Services::where('division_id', $request->division_id)
            ->whereNull('section_id')  // Only get services directly under division
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'service_name' => $item->service_name,
                    'service_description' => $item->service_description,
                    'service_availability' => $item->service_availability,
                    'service_active' => $item->service_active
                ];
            });

        return $division_services;
    }

    public function getSectionServices(Request $request)
    {
        $section_services = Services::where('division_id', $request->division_id)
            ->where('section_id', $request->section_id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'service_name' => $item->service_name,
                    'service_description' => $item->service_description,
                    'service_availability' => $item->service_availability,
                    'service_active' => $item->service_active
                ];
            });

        return $section_services;
    }

    public function addService(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'section_id' => 'nullable|exists:sections,id',
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'service_availability' => 'nullable|string',
            'service_active' => 'nullable|boolean'
        ]);

        $service = new Services();
        $service->service_name = strtoupper($request->service_name);
        $service->service_description = $request->service_description;
        $service->service_availability = $request->service_availability;
        $service->service_active = $request->has('service_active') ? $request->service_active : true;
        $service->division_id = $request->division_id;
        $service->section_id = $request->section_id;
        $service->save();

        return redirect()->back()->with('message', 'Service added successfully.');
    }

    // Method to handle adding service directly to division
    public function addServiceToDivision(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'service_availability' => 'nullable|string',
            'service_active' => 'nullable|boolean'
        ]);

        $service = new Services();
        $service->service_name = strtoupper($request->service_name);
        $service->service_description = $request->service_description;
        $service->service_availability = $request->service_availability;
        $service->service_active = $request->has('service_active') ? $request->service_active : true;
        $service->division_id = $request->division_id;
        $service->section_id = null; // No section for direct division service
        $service->save();

        return redirect()->back()->with('message', 'Service added to division successfully.');
    }

    // Method to handle adding service to section
    public function addServiceToSection(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'section_id' => 'required|exists:sections,id',
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'service_availability' => 'nullable|string',
            'service_active' => 'nullable|boolean'
        ]);

        $service = new Services();
        $service->service_name = strtoupper($request->service_name);
        $service->service_description = $request->service_description;
        $service->service_availability = $request->service_availability;
        $service->service_active = $request->has('service_active') ? $request->service_active : true;
        $service->division_id = $request->division_id;
        $service->section_id = $request->section_id;
        $service->save();

        return redirect()->back()->with('message', 'Service added to section successfully.');
    }

    // Get a specific service details
    public function getService(Request $request)
    {
        $service = Services::find($request->service_id);
        
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        return new ServicesResource($service);
    }

    // Get all active services
    public function getAllActiveServices()
    {
        $services = Services::where('service_active', true)->get();
        return ServicesResource::collection($services);
    }

    // Add this to your ServicesController.php
    public function divisionServicesIndex()
    {
        // Get all divisions with their sections and services
        $divisions = Division::with([
            'sections',
            'sections.services',  // Services that belong to sections
            'services'           // Services that belong directly to divisions
        ])->get();

        $user = Auth::user();

        return Inertia::render('Libraries/Division-Services/Index', [
            'divisions' => $divisions,
            'user' => $user
        ]);
    }
}