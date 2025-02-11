<?php

namespace App\Http\Controllers;

use App\Models\Assignatorees;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AssignatoreesController extends Controller
{
    public function index(Request $request)
    {
        //get user
        $user = Auth::user();

        $search = $request->search;

        $assignatorees = Assignatorees::when($search, function ($query,  $search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('designation', 'like', '%' . $search . '%');
        })
        ->where('office_id', $user->office_id)
        ->orderByDesc('created_at')
        ->paginate(10);

        return Inertia::render('Libraries/Assignatorees/Index')
                    ->with('assignatorees', $assignatorees);
    }

    public function store(Request $request)
    {
        //get user
        $user = Auth::user();

        $assignatoree = new Assignatorees();
        $assignatoree->office_id = $user->office_id;
        $assignatoree->name = $request->name;
        $assignatoree->designation = $request->designation;
        $assignatoree->save();

        return Redirect::back();
       
    }

    public function update(Request $request)
    {
        $assignatoree = Assignatorees::findorFail($request->id);
        $assignatoree->name = $request->name;
        $assignatoree->designation = $request->designation;
        $assignatoree->update();

        return Redirect::back();
       
    }

    public function destroy(Request $request)
    {
        $assignatoree = Assignatorees::findorFail($request->id);
        $assignatoree->delete();

        return Redirect::back();
       
    }
}
