<?php

namespace App\Http\Controllers;
use App\Models\Section;
use Inertia\Inertia;
use App\Models\SubSection;
use App\Models\Division;
use App\Models\SectionSubSection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Division as DivisionSectionsResource;
use App\Http\Resources\SectionSubSection as SectionSubSectionResource;


class DivisionSectionController extends Controller
{
    public function index()
    {
        $division_sections = Division::all();

        $data = DivisionSectionsResource::collection($division_sections);
        $user = Auth::user();

        return Inertia::render('Libraries/Division-Sections/Index')
            ->with('division_sections', $data)
            ->with('user',  $user);
    }

    public function getDivisionSections(Request $request)
    {
        $division_sections = Section::where('division_id',$request->code)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'section_name' => $item->section_name
                ];
            });

        return $division_sections;
    }    


    public function storeDivision(Request $request)
    {
        $division = new Division();
        $division->division_name = strtoupper($request->division_name);
        $division->slug = Str::slug($request->division_name, '-');
        $division->save();
    }

    
    public function storeSection(Request $request)
    {
        //dd($request->all());
        $section = new Section();
        $section->division_id = $request->division_id;
        $section->section_name = strtoupper($request->section_name);
        $section->save();
    }



    public function section_index(Request $request)
    {
        //get user
        $user = Auth::user();

        $division = $request->division;
        $section = $request->section;

        // get section sub sections
        $section_sub_sections = SectionSubSection::where('section_id',$request->section['id'])->get();
        $section_sub_sections = SectionSubSectionResource::collection($section_sub_sections);

        $sub_sections = $section_sub_sections->pluck('sub_sectiont');

        return Inertia::render('Libraries/Division-Sections/Views/SubDivisionView')
        ->with('section', $section)
        ->with('division', $division)
        ->with('sub_sections', $sub_sections)
        ->with('user',  $user);
 
    }

    public function sub_section_type($request)
    {
        //dd($request->all());

        //get user
        $user = Auth::user();

        $division = $request->division;
        $section = $request->section;
        $sub_section = $request->sub_section;

        if($sub_section){  
            $sub_section_types = SubSectionType::where('sub_section_id',$request->sub_section['id'])->get();
            $sub_section_types = SubSectionTypeResource::collection($sub_section_types);
     
            $sub_section_types = $sub_section_types->pluck('type_name');
    
            return $sub_section_types;
        }
        else{
            return [];
        }

       
    }


    
}
