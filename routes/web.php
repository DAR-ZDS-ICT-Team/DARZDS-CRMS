<?php

use Inertia\Inertia;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\AssignatoreesController;
use App\Http\Controllers\ShowDateCSFFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SubSectionController;
use App\Http\Controllers\SurveyFormController;
use App\Http\Controllers\DivisionSectionController;
use App\Http\Controllers\ServicesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        // 'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/divisions/csf/offices', [SurveyFormController::class, 'offices_index'])->name('offices_index');
Route::get('/divisions/csf/divisions', [SurveyFormController::class, 'divisions_index'])->name('divisions_index');
Route::get('/divisions/csf/division_sections', [SurveyFormController::class, 'division_sections_index'])->name('division_sections_index');
Route::get('/divisions/csf/section_services_index', [SurveyFormController::class, 'section_services_index'])->name('section_services_index');
Route::get('/divisions/csf/services/check-sub-services', [SurveyFormController::class, 'checkServiceSubServices'])->name('services.checkSubServices');// Route::get('/divisions/csf/section/sub-sections', [SurveyFormController::class, 'getSectionSubSections'])->name('getSectionSubSections');


Route::get('/divisions/csf', [SurveyFormController::class, 'index'])->name('csf_form');
Route::get('/divisions/csf/store', [SurveyFormController::class, 'store'])->name('store_form');
Route::get('/form/csf/msg', [SurveyFormController::class, 'msg_index'])->name('msg_index');
Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha')->middleware('web');
// Route::post('/captcha/verify', [SurveyFormController::class, 'verifyCaptcha']);
Route::post('/csf_submission', [SurveyFormController::class, 'store']);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::middleware([CheckAdmin::class])->group(function () {
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts');
        Route::post('/accounts/add', [AccountController::class, 'store']);
        Route::post('/accounts/update', [AccountController::class, 'update']);
        Route::post('/accounts/reset-password', [AccountController::class, 'resetPassword']);   
        Route::get('/libraries', function () {
            return Inertia::render('Libraries/Divisions/Index');
        })->name('libraries');
        Route::get('/offices', [OfficeController::class, 'index'])->name('offices');
        Route::post('/offices/add', [OfficeController::class, 'store']);
        Route::post('/offices/update', [OfficeController::class, 'update']);
        Route::get('/assignatorees', [AssignatoreesController::class, 'index'])->name('assignatorees');
        Route::post('/assignatorees/add', [AssignatoreesController::class, 'store']);
        Route::post('/assignatorees/update', [AssignatoreesController::class, 'update']);
        Route::post('/assignatorees/delete', [AssignatoreesController::class, 'destroy']);
        Route::get('/show-date-csf-form', [ShowDateCSFFormController::class, 'index'])->name('showdate');
        Route::post('/show-date-csf-form/update', [ShowDateCSFFormController::class, 'update']);
    });

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/profile', function () {
        return Inertia::render('Profile/Show');
    })->name('profile');
    Route::get('/division_sections', [DivisionSectionController::class, 'index'])->name('division_sections');
    Route::get('/division_sections/storeDivision', [DivisionSectionController::class, 'storeDivision'])->name('store_division');
    Route::get('/division_sections/updateDivision', [DivisionSectionController::class, 'updateDivision'])->name('update_division');
    Route::get('/division_sections/destroyDivision', [DivisionSectionController::class, 'destroyDivision'])->name('destroy_division');
    Route::get('/division_sections/storeSection', [DivisionSectionController::class, 'storeSection'])->name('store_section');
    Route::get('/division_sections/updateSection', [DivisionSectionController::class, 'updateSection'])->name('update_section');
    Route::get('/division_sections/destroySection', [DivisionSectionController::class, 'destroySection'])->name('destroy_section');
    // Route::get('/division/sections', [DivisionSectionController::class, 'getDivisionSections']);
    // Route::post('/divisions/add', [DivisionSectionController::class, 'storeDivision']);
    // Route::post('/divisions/section/add', [DivisionSectionController::class, 'storeSection']);

    // Service routes 
    Route::post('/divisions/services', [ServicesController::class, 'index'])->name('services_index');
    Route::post('/divisions/service/store', [ServicesController::class, 'storeServices'])->name('store_services');
    Route::post('/divisions/service/update', [ServicesController::class, 'updateServices'])->name('update_services');
    Route::post('/divisions/service/destroy', [ServicesController::class, 'destroyServices'])->name('destroy_services');
    // Route::post('/divisions/service/section_services', [ServicesController::class, 'sectionServicesIndex'])->name('section_services');
    // Route::post('/divisions/service/division_services', [ServicesController::class, 'divisionServicesIndex'])->name('division_services');
    // Route::post('/divisions/service/add', [ServicesController::class, 'addServiceToDivision']);
    // Route::post('/divisions/section/service/add', [ServicesController::class, 'addServiceToSection']);
    // Route::get('/divisions/services', [ServicesController::class, 'getDivisionServices']);
    // Route::get('/divisions/section/services', [ServicesController::class, 'getSectionServices']);
    // Route::get('/divisions/service/{service_id}', [ServicesController::class, 'getService']);
    // Route::get('/divisions/services/active', [ServicesController::class, 'getAllActiveServices']);
    // Route::get('/services', [ServicesController::class, 'index'])->name('services');
    // Route::get('/division-services', [ServicesController::class, 'divisionServicesIndex'])
    // ->name('division.services');
    
    // Route::get('/division_section/section', [DivisionSectionController::class , 'section_index'])->name('sections');
    Route::get('/csi', [ReportController::class , 'index']);
    Route::get('/csi/view', [ReportController::class , 'view']);
    Route::get('/csi/all-sections', [ReportController::class , 'all_sections']);
   
    Route::get('/csi/generate/all-sections/monthly', [ReportController::class, 'generateAllSectionReports']);
    Route::post('/csi/generate', [ReportController::class, 'generateReports']);

});




