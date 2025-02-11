<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Office;
use App\Models\SubSection;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Http\Resources\Section as SectionResource;
use App\Http\Resources\Account as AccountResource;
use App\Http\Resources\Divisions as DivisionsResource;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->search;

        $offices = Office::all();
        $divisions = Division::all();
        

        $accounts = User::when($search, function ($query,  $search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('office_id', 'like', '%' . $search . '%');
        })
        ->where('office_id',$user->office_id)
        ->orderByDesc('created_at')
        ->paginate(10);

        $data = AccountResource::collection($accounts);

        return Inertia::render('Account/Index')
                    ->with('accounts', $data)
                    ->with('offices', $offices)
                    ->with('divisions', $divisions);
    }

    public function resetPassword(Request $request)
    {
        $default_password = "*1234#";
        $account = User::findorFail($request->id);

        $account->password = Hash::make($default_password);
        $account->update();

        return Redirect::back();
       
    }

    public function store(Request $request)
    {
        $account = new User();
        $account->name = $request->name;
        $account->designation = strtoupper($request->designation);
        $account->email = $request->email;
        $account->password = Hash::make($request->password);
        $account->office_id = $request->office;
        $account->account_type = $request->account_type;
        $account->division_id = $request->division;
        $account->section_id = $request->section;
        $account->save();

        return Redirect::back();
       
    }

    public function update(Request $request)
    {
        $account = User::findorFail($request->id);
        $account->name = $request->name;
        $account->designation = strtoupper($request->designation);
        $account->email = $request->email;
        $account->office_id = $request->office;
        $account->account_type = $request->account_type;
        $account->division_id = $request->division;
        $account->section_id = $request->section;
        $account->update();

        return Redirect::back();
       
    }
}
