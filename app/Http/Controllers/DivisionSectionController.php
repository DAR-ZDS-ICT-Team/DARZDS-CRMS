<?php

namespace App\Http\Controllers;
use App\Models\Section;
use App\Models\Services;
use Inertia\Inertia;
use App\Models\SubSection;
use App\Models\SubSectionType;
use App\Models\Division;
use App\Models\SectionSubSection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Division as DivisionSectionsResource;
use App\Http\Resources\SectionSubSection as SectionSubSectionResource;
use App\Http\Resources\SubSectionType as SubSectionTypeResource;


class DivisionSectionController extends Controller
{
    /**
     * Display sections or services for a division
     */
    public function index($division_id)
    {
        $division = Division::findOrFail($division_id);

        // Get all sections for this division
        $sections = Section::where('division_id', $division_id)->get();

        if ($sections->isNotEmpty()) {
            return Inertia::render('Sections/Index', [
                'sections' => $sections,
                'division' => $division
            ]);
        }

        // No sections → get services directly under division
        $services = Services::where('division_id', $division_id)
                            ->whereNull('section_id')
                            ->get();

        if ($services->isNotEmpty()) {
            return Inertia::render('Services/Index', [
                'services' => $services,
                'division' => $division,
                'section'  => null
            ]);
        }

        // If neither → go to survey form
        return Inertia::render('Survey/Form', [
            'division' => $division
        ]);
    }

    /**
     * CREATE - Division
     */
    public function storeDivision(Request $request)
    {
        $request->validate([
            'division_name' => 'required|string|max:255',
            'office_id' => 'required|exists:offices,id',
        ]);

        Division::create([
            'office_id' => $request->office_id,
            'division_name' => $request->division_name,
            'slug' => Str::slug($request->division_name, '-'),
        ]);

        return back()->with('message', 'Division created successfully.');
    }

    /**
     * CREATE - Section
     */
    public function storeSection(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'section_name' => 'required|string|max:255',
        ]);

        $division = Division::findOrFail($request->division_id);

        Section::create([
            'office_id' => $division->office_id,
            'division_id' => $request->division_id,
            'section_name' => $request->section_name,
        ]);

        return back()->with('message', 'Section created successfully.');
    }

    /**
     * UPDATE - Division
     */
    public function updateDivision(Request $request, $id = null)
    {
        $request->validate([
            'division_name' => 'required|string|max:255',
            'office_id' => 'required|exists:offices,id',
        ]);

        $divisionId = $id ?? $request->id;
        $division = Division::findOrFail($divisionId);
        $division->update([
            'office_id' => $request->office_id,
            'division_name' => $request->division_name,
            'slug' => Str::slug($request->division_name, '-'),
        ]);

        return back()->with('message', 'Division updated successfully.');
    }

    /**
     * UPDATE - Section
     */
    public function updateSection(Request $request, $id = null)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'division_id' => 'required|exists:divisions,id',
        ]);

        $sectionId = $id ?? $request->id;
        $section = Section::findOrFail($sectionId);
        $division = Division::findOrFail($request->division_id);
        $section->update([
            'office_id' => $division->office_id,
            'division_id' => $request->division_id,
            'section_name' => $request->section_name,
        ]);

        return back()->with('message', 'Section updated successfully.');
    }

    /**
     * DELETE - Division
     */
    public function destroyDivision(Request $request, $id = null)
    {
        $divisionId = $id ?? $request->id;
        $division = Division::findOrFail($divisionId);
        $division->delete();

        return back()->with('message', 'Division deleted successfully.');
    }

    /**
     * DELETE - Section
     */
    public function destroySection(Request $request, $id = null)
    {
        $sectionId = $id ?? $request->id;
        $section = Section::findOrFail($sectionId);
        $section->delete();

        return back()->with('message', 'Section deleted successfully.');
    }
}
