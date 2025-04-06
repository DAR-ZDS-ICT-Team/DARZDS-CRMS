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
use App\Http\Resources\SubSectionType as SubSectionTypeResource;

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

    public function division_index()
    {
        //
    }

    public function section_index()
    {
        //
    }

    public function getDivisionServices(Request $request)
    {
        $division_services = Services::where('division_id',$request->code)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'service_name' => $item->service_name
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
                    'service_name' => $item->service_name
                ];
            });

        return $section_services;
    }

    public function storeServices(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'section_id' => 'nullable|exists:sections,id',
            'division_id' => 'nullable|exists:divisions,id',
        ]);

        if (!$request->section_id && !$request->division_id) {
            return response()->json(['error' => 'Either section_id or division_id is required.'], 422);
        }

        if ($request->section_id && $request->division_id) {
            return response()->json(['error' => 'A service must belong to either a section or a division, not both.'], 422);
        }

        $service = new Services();
        $service->service_name = strtoupper($request->service_name);
        $service->section_id = $request->section_id;
        $service->division_id = $request->division_id;
        $service->save();

        return response()->json(['message' => 'Service created successfully.'], 200);
    }

}
