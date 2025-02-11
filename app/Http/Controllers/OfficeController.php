<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Resources\Office as OfficeResource;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get user
        $user = Auth::user();

        $search = $request->search;

        $offices = Office::all();

        $offices = Office::when($search, function ($query,  $search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('short_name', 'like', '%' . $search . '%')
                  ->orWhere('order', 'like', '%' . $search . '%');
        })
        ->orderByDesc('created_at')
        ->paginate(10);

        return Inertia::render('Libraries/Offices/Index')
                    ->with('offices', $offices)
                    ->with('user', $user);
    }

    public function store(Request $request)
    {
        $office = new Office();
        $office->name = $request->name;
        $office->code = $request->code;
        $office->short_name = $request->short_name;
        $office->order = $request->order;
        $office->save();

        return Redirect::back();
       
    }

    public function update(Request $request)
    {
        $office = Office::findorFail($request->id);
        $office->name = $request->name;
        $office->code = $request->code;
        $office->short_name = $request->short_name;
        $office->order = $request->order;
        $office->update();

        return Redirect::back();
       
    }
}
