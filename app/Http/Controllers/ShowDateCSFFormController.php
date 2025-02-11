<?php

namespace App\Http\Controllers;

use App\Models\ShowDateCsfForm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ShowDateCSFFormController extends Controller
{
    public function index(Request $request)
    {
        $date_display = ShowDateCsfForm::all();

        return Inertia::render('Libraries/CSFFormDate/Index')
                    ->with('date_display', $date_display);
    }


    public function update(Request $request)
    {
        //dd($request->value);
        $date_display = ShowDateCsfForm::findorFail(1);
        $date_display->is_displayed = $request->value;
        $date_display->update();

        return Redirect::back();
       
    }

}
