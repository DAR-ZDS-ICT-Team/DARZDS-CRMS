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
Route::get('/divisions/csf/section/sub-sections', [SurveyFormController::class, 'getSectionSubSections'])->name('getSectionSubSections');
Route::get('/divisions/csf/sub-section/types', [SurveyFormController::class, 'getSubSectionTypes'])->name('getSubSectionTypes');
Route::get('/divisions/csf', [SurveyFormController::class, 'index'])->name('csf_form');
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
    Route::get('/division/sections', [DivisionSectionController::class, 'getDivisionSections']);
    Route::post('/divisions/add', [DivisionSectionController::class, 'storeDivision']);
    Route::post('/divisions/section/add', [DivisionSectionController::class, 'storeSection']);
    
    Route::get('/division_section/section', [DivisionSectionController::class , 'section_index'])->name('sections');
    Route::get('/csi', [ReportController::class , 'index']);
    Route::get('/csi/view', [ReportController::class , 'view']);
    Route::get('/csi/all-sections', [ReportController::class , 'all_sections']);
   
    Route::get('/csi/generate/all-sections/monthly', [ReportController::class, 'generateAllSectionReports']);
    Route::post('/csi/generate', [ReportController::class, 'generateReports']);



});




