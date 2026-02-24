<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Section;
use App\Models\Services;
use App\Models\Office;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Index');
    }

    public function divisions()
    {
        $divisions = Division::with('office')->orderBy('division_name')->get();
        $offices = Office::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($office) => [
                'id' => $office->id,
                'name' => $office->name,
            ])
            ->values();

        return Inertia::render('Settings/Divisions/Index', [
            'divisions' => $divisions,
            'offices' => $offices,
        ]);
    }

    public function sections()
    {
        $sections = Section::with('division')->orderBy('section_name')->get();
        $divisions = Division::orderBy('division_name')->get();

        return Inertia::render('Settings/Sections/Index', [
            'sections' => $sections,
            'divisions' => $divisions,
        ]);
    }

    public function services()
    {
        $services = Services::with(['division', 'section'])->orderBy('service_name')->get();
        $divisions = Division::orderBy('division_name')->get();
        $sections = Section::orderBy('section_name')->get();

        return Inertia::render('Settings/Services/Index', [
            'services' => $services,
            'divisions' => $divisions,
            'sections' => $sections,
        ]);
    }
}
