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
use App\Models\Division;
use Illuminate\Support\Facades\Auth;
use App\Models\Services;
use App\Models\CSFForm;
use App\Models\CustomerCCRating;
use App\Models\CustomerAttributeRating;
use App\Models\Dimension;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;



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

$resolveDateRange = function ($request) {
    $type = $request->input('period_type');
    $month = $request->input('selected_month');
    $quarter = $request->input('selected_quarter');
    $year = $request->input('selected_year');

    if ($type === 'By Month' && $month && $year) {
        $monthNumber = Carbon::parse("1 {$month}")->month;
        $start = Carbon::create((int) $year, $monthNumber, 1)->startOfDay();
        $end = (clone $start)->endOfMonth()->endOfDay();
        return [$start, $end];
    }

    if ($type === 'By Quarter' && $quarter && $year) {
        $quarterMap = [
            'FIRST QUARTER' => [1, 3],
            'SECOND QUARTER' => [4, 6],
            'THIRD QUARTER' => [7, 9],
            'FOURTH QUARTER' => [10, 12],
        ];
        if (isset($quarterMap[$quarter])) {
            [$startMonth, $endMonth] = $quarterMap[$quarter];
            $start = Carbon::create((int) $year, $startMonth, 1)->startOfDay();
            $end = Carbon::create((int) $year, $endMonth, 1)->endOfMonth()->endOfDay();
            return [$start, $end];
        }
    }

    if ($type === 'By Year/Annual' && $year) {
        $start = Carbon::create((int) $year, 1, 1)->startOfDay();
        $end = Carbon::create((int) $year, 12, 31)->endOfDay();
        return [$start, $end];
    }

    return [null, null];
};

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
])->group(function () use ($resolveDateRange) {
    Route::middleware([CheckAdmin::class])->group(function () {
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts');
        Route::post('/accounts/add', [AccountController::class, 'store']);
        Route::post('/accounts/update', [AccountController::class, 'update']);
        Route::post('/accounts/reset-password', [AccountController::class, 'resetPassword']);   
        // Route::get('/libraries', function () {
        //     return Inertia::render('Libraries/Divisions/Index');
        // })->name('libraries');
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

    Route::get('/libraries', function () {
        $user = Auth::user();

        $divisions = Division::with([
            'sections' => fn ($q) => $q->orderBy('section_name'),
            'sections.services' => fn ($q) => $q->orderBy('service_name'),
            'services' => fn ($q) => $q->whereNull('section_id')->orderBy('service_name'),
        ])
            ->where('office_id', $user->office_id)
            ->orderBy('division_name')
            ->get();

        return Inertia::render('Libraries/Divisions/Index', [
            'divisions' => $divisions,
            'filters' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
        ]);
    })->name('libraries');

    Route::get('/libraries/overall/services', function () use ($resolveDateRange) {
        $user = Auth::user();
        [$startDate, $endDate] = $resolveDateRange(request());

        $services = Services::whereHas('division', function ($q) use ($user) {
                $q->where('office_id', $user->office_id);
            })
            ->orderBy('service_name')
            ->get(['id', 'service_name', 'service_type']);

        $csfBase = CSFForm::where('office_id', $user->office_id);
        if ($startDate && $endDate) {
            $csfBase->whereBetween('created_at', [$startDate, $endDate]);
        }

        $serviceCounts = (clone $csfBase)->select(
                'service_id',
                DB::raw('count(distinct customer_id) as responses'),
                DB::raw('count(*) as transactions')
            )
            ->whereNotNull('service_id')
            ->whereNotNull('customer_id')
            ->groupBy('service_id')
            ->get()
            ->keyBy('service_id');

        $serviceStats = $services->map(function ($service) use ($serviceCounts) {
            $row = $serviceCounts->get($service->id);

            return [
                'id' => $service->id,
                'service_name' => $service->service_name,
                'service_type' => $service->service_type,
                'responses' => (int) ($row->responses ?? 0),
                'transactions' => (int) ($row->transactions ?? 0),
            ];
        });

        $internalServiceStats = $serviceStats
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'internal')
            ->values();

        $externalServiceStats = $serviceStats
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'external')
            ->values();

        return Inertia::render('Libraries/Divisions/ServicesTable', [
            'service_stats' => $serviceStats,
            'internal_service_stats' => $internalServiceStats,
            'external_service_stats' => $externalServiceStats,
            'filters' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
        ]);
    })->name('libraries.overall.services');

    Route::get('/libraries/overall/demographic', function () use ($resolveDateRange) {
        $user = Auth::user();
        [$startDate, $endDate] = $resolveDateRange(request());

        $csfBase = CSFForm::where('office_id', $user->office_id);
        if ($startDate && $endDate) {
            $csfBase->whereBetween('created_at', [$startDate, $endDate]);
        }

        $customerIds = (clone $csfBase)
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');

        $baseCustomers = Customer::whereIn('id', $customerIds);

        $clientTypes = (clone $baseCustomers)
            ->selectRaw("COALESCE(NULLIF(client_type,''),'Not Indicated') as label, COUNT(*) as count")
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $ageGroups = (clone $baseCustomers)
            ->selectRaw("COALESCE(NULLIF(age_group,''),'Not Indicated') as label, COUNT(*) as count")
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $sexes = (clone $baseCustomers)
            ->selectRaw("COALESCE(NULLIF(sex,''),'Not Indicated') as label, COUNT(*) as count")
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $total = (clone $baseCustomers)->count();

        return Inertia::render('Libraries/Divisions/Demographic', [
            'client_types' => $clientTypes,
            'age_groups' => $ageGroups,
            'sexes' => $sexes,
            'total_customers' => $total,
            'filters' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
        ]);
    })->name('libraries.overall.demographic');

    Route::get('/libraries/overall/cc', function () use ($resolveDateRange) {
        $user = Auth::user();
        [$startDate, $endDate] = $resolveDateRange(request());

        $csfBase = CSFForm::where('office_id', $user->office_id);
        if ($startDate && $endDate) {
            $csfBase->whereBetween('created_at', [$startDate, $endDate]);
        }

        $customerIds = (clone $csfBase)
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');

        $totalCustomers = $customerIds->count();

        $ccQuery = CustomerCCRating::select('cc_id', 'answer', DB::raw('count(*) as count'))
            ->whereIn('customer_id', $customerIds);
        if ($startDate && $endDate) {
            $ccQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $counts = $ccQuery->groupBy('cc_id', 'answer')
            ->get()
            ->groupBy('cc_id')
            ->map(function ($group) {
                return $group->keyBy(function ($item) {
                    return (string) $item->answer;
                });
            });

        $buildRows = function (int $ccId, array $options) use ($counts, $totalCustomers) {
            $rows = [];
            $answeredTotal = 0;
            $ccCounts = $counts->get($ccId, collect());

            foreach ($options as $option) {
                $answerKey = (string) $option['value'];
                $count = (int) ($ccCounts->get($answerKey)->count ?? 0);
                $answeredTotal += $count;
                $percentage = $totalCustomers > 0 ? round(($count / $totalCustomers) * 100) : 0;

                $rows[] = [
                    'label' => $option['label'],
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            }

            $noAnswerCount = max(0, $totalCustomers - $answeredTotal);
            $noAnswerPercentage = $totalCustomers > 0 ? round(($noAnswerCount / $totalCustomers) * 100) : 0;

            $rows[] = [
                'label' => 'No Answer',
                'count' => $noAnswerCount,
                'percentage' => $noAnswerPercentage,
            ];

            return $rows;
        };

        $tables = [
            [
                'title' => 'CC 1',
                'rows' => $buildRows(1, [
                    ['value' => 1, 'label' => 'CC 1.1 I know what a CC is and I saw this office\'s CC.'],
                    ['value' => 2, 'label' => 'CC 1.2 I know what a CC is but I did not see this office\'s CC.'],
                    ['value' => 3, 'label' => 'CC 1.3 I learned of the CC only when I saw this office\'s CC.'],
                    ['value' => 4, 'label' => 'CC 1.4 I do not know what a CC is and I did not see this office\'s CC.'],
                ]),
            ],
            [
                'title' => 'CC 2',
                'rows' => $buildRows(2, [
                    ['value' => 1, 'label' => 'CC 2.1 Easy to see'],
                    ['value' => 2, 'label' => 'CC 2.2 Somewhat easy to see'],
                    ['value' => 3, 'label' => 'CC 2.3 Difficult to see'],
                    ['value' => 4, 'label' => 'CC 2.4 Not visible at all'],
                    ['value' => 5, 'label' => 'N/A'],
                ]),
            ],
            [
                'title' => 'CC 3',
                'rows' => $buildRows(3, [
                    ['value' => 1, 'label' => 'CC 3.1 Helped very much'],
                    ['value' => 2, 'label' => 'CC 3.2 Somewhat helped'],
                    ['value' => 3, 'label' => 'CC 3.3 Did not help'],
                    ['value' => 4, 'label' => 'N/A'],
                ]),
            ],
        ];

        return Inertia::render('Libraries/Divisions/CcTable', [
            'cc_tables' => $tables,
            'total_customers' => $totalCustomers,
            'filters' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
        ]);
    })->name('libraries.overall.cc');

    Route::get('/libraries/overall/sqd', function () use ($resolveDateRange) {
        $user = Auth::user();
        [$startDate, $endDate] = $resolveDateRange(request());

        $csfBase = CSFForm::where('office_id', $user->office_id);
        if ($startDate && $endDate) {
            $csfBase->whereBetween('created_at', [$startDate, $endDate]);
        }

        $customerIds = (clone $csfBase)
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');

        $dimensions = Dimension::all();

        $ratingsQuery = CustomerAttributeRating::select('dimension_id', 'rate_score', DB::raw('count(*) as count'))
            ->whereIn('customer_id', $customerIds);
        if ($startDate && $endDate) {
            $ratingsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $counts = $ratingsQuery->groupBy('dimension_id', 'rate_score')
            ->get()
            ->groupBy('dimension_id')
            ->map(function ($group) {
                return $group->keyBy('rate_score');
            });

        $dimensionOrder = [
            'responsiveness' => 'Responsiveness',
            'reliability' => 'Reliability',
            'access-and-facilities' => 'Access and Facilities',
            'communication' => 'Communication',
            'costs' => 'Costs',
            'integrity' => 'Integrity',
            'assurance' => 'Assurance',
            'outcome' => 'Outcome',
            'overall' => 'Overall Satisfaction',
        ];

        $dimensionMap = $dimensions->keyBy(function ($dimension) {
            return strtolower($dimension->slug);
        });

        $rows = [];
        $totals = [
            'strongly_disagree' => 0,
            'disagree' => 0,
            'neither' => 0,
            'agree' => 0,
            'strongly_agree' => 0,
            'na' => 0,
            'responses' => 0,
        ];

        foreach ($dimensionOrder as $slug => $label) {
            $dimension = $dimensionMap->get($slug);
            if (!$dimension) {
                continue;
            }

            $dimCounts = $counts->get($dimension->id, collect());
            $stronglyDisagree = (int) ($dimCounts->get(1)->count ?? 0);
            $disagree = (int) ($dimCounts->get(2)->count ?? 0);
            $neither = (int) ($dimCounts->get(3)->count ?? 0);
            $agree = (int) ($dimCounts->get(4)->count ?? 0);
            $stronglyAgree = (int) ($dimCounts->get(5)->count ?? 0);
            $na = (int) ($dimCounts->get(6)->count ?? 0);
            $responses = $stronglyDisagree + $disagree + $neither + $agree + $stronglyAgree + $na;
            $denominator = $responses - $na;
            $rating = $denominator > 0 ? round((($agree + $stronglyAgree) / $denominator) * 100, 1) : 0;

            $rows[] = [
                'label' => $label,
                'strongly_disagree' => $stronglyDisagree,
                'disagree' => $disagree,
                'neither' => $neither,
                'agree' => $agree,
                'strongly_agree' => $stronglyAgree,
                'na' => $na,
                'responses' => $responses,
                'rating' => $rating,
            ];

            $totals['strongly_disagree'] += $stronglyDisagree;
            $totals['disagree'] += $disagree;
            $totals['neither'] += $neither;
            $totals['agree'] += $agree;
            $totals['strongly_agree'] += $stronglyAgree;
            $totals['na'] += $na;
            $totals['responses'] += $responses;
        }

        $totalDenominator = $totals['responses'] - $totals['na'];
        $totals['rating'] = $totalDenominator > 0
            ? round((($totals['agree'] + $totals['strongly_agree']) / $totalDenominator) * 100, 1)
            : 0;

        return Inertia::render('Libraries/Divisions/SqdTable', [
            'sqd_rows' => $rows,
            'sqd_totals' => $totals,
            'filters' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
        ]);
    })->name('libraries.overall.sqd');

    Route::get('/libraries/overall/services-rating', function () use ($resolveDateRange) {
        $user = Auth::user();
        [$startDate, $endDate] = $resolveDateRange(request());

        $services = Services::whereHas('division', function ($query) use ($user) {
                $query->where('office_id', $user->office_id);
            })
            ->orderBy('service_name')
            ->get(['id', 'service_name', 'service_type']);

        $csfBase = CSFForm::where('office_id', $user->office_id);
        if ($startDate && $endDate) {
            $csfBase->whereBetween('created_at', [$startDate, $endDate]);
        }

        $serviceRatings = $services->map(function ($service) use ($csfBase, $startDate, $endDate) {
            $customerIds = (clone $csfBase)
                ->where('service_id', $service->id)
                ->whereNotNull('customer_id')
                ->distinct()
                ->pluck('customer_id');

            if ($customerIds->isEmpty()) {
                return [
                    'id' => $service->id,
                    'service_name' => $service->service_name,
                    'service_type' => $service->service_type,
                    'rating' => null,
                ];
            }

            $baseRatings = CustomerAttributeRating::whereIn('customer_id', $customerIds);
            if ($startDate && $endDate) {
                $baseRatings->whereBetween('created_at', [$startDate, $endDate]);
            }
            $denominator = (clone $baseRatings)
                ->whereIn('rate_score', [1, 2, 3, 4, 5])
                ->count();
            $positive = (clone $baseRatings)
                ->whereIn('rate_score', [4, 5])
                ->count();

            $rating = $denominator > 0 ? round(($positive / $denominator) * 100, 2) : null;

            return [
                'id' => $service->id,
                'service_name' => $service->service_name,
                'service_type' => $service->service_type,
                'rating' => $rating,
            ];
        });

        $internalServiceRatings = $serviceRatings
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'internal')
            ->values();

        $externalServiceRatings = $serviceRatings
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'external')
            ->values();

        return Inertia::render('Libraries/Divisions/ServicesOverallTable', [
            'service_ratings' => $serviceRatings,
            'internal_service_ratings' => $internalServiceRatings,
            'external_service_ratings' => $externalServiceRatings,
            'filters' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
        ]);
    })->name('libraries.overall.services_rating');

    Route::get('/libraries/report', function () use ($resolveDateRange) {
        $user = Auth::user();
        [$startDate, $endDate] = $resolveDateRange(request());

        $services = Services::whereHas('division', function ($q) use ($user) {
                $q->where('office_id', $user->office_id);
            })
            ->orderBy('service_name')
            ->get(['id', 'service_name', 'service_type']);

        $csfBase = CSFForm::where('office_id', $user->office_id);
        if ($startDate && $endDate) {
            $csfBase->whereBetween('created_at', [$startDate, $endDate]);
        }

        $customerIds = (clone $csfBase)
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');

        $totalCustomers = $customerIds->count();
        $baseCustomers = Customer::whereIn('id', $customerIds);

        $clientTypes = (clone $baseCustomers)
            ->selectRaw("COALESCE(NULLIF(client_type,''),'Not Indicated') as label, COUNT(*) as count")
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $ageGroups = (clone $baseCustomers)
            ->selectRaw("COALESCE(NULLIF(age_group,''),'Not Indicated') as label, COUNT(*) as count")
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $sexes = (clone $baseCustomers)
            ->selectRaw("COALESCE(NULLIF(sex,''),'Not Indicated') as label, COUNT(*) as count")
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $ccQuery = CustomerCCRating::select('cc_id', 'answer', DB::raw('count(*) as count'))
            ->whereIn('customer_id', $customerIds);
        if ($startDate && $endDate) {
            $ccQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $ccCounts = $ccQuery->groupBy('cc_id', 'answer')
            ->get()
            ->groupBy('cc_id')
            ->map(function ($group) {
                return $group->keyBy(function ($item) {
                    return (string) $item->answer;
                });
            });

        $buildCcRows = function (int $ccId, array $options) use ($ccCounts, $totalCustomers) {
            $rows = [];
            $answeredTotal = 0;
            $ccSet = $ccCounts->get($ccId, collect());

            foreach ($options as $option) {
                $answerKey = (string) $option['value'];
                $count = (int) ($ccSet->get($answerKey)->count ?? 0);
                $answeredTotal += $count;
                $percentage = $totalCustomers > 0 ? round(($count / $totalCustomers) * 100) : 0;

                $rows[] = [
                    'label' => $option['label'],
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            }

            $noAnswerCount = max(0, $totalCustomers - $answeredTotal);
            $noAnswerPercentage = $totalCustomers > 0 ? round(($noAnswerCount / $totalCustomers) * 100) : 0;

            $rows[] = [
                'label' => 'No Answer',
                'count' => $noAnswerCount,
                'percentage' => $noAnswerPercentage,
            ];

            return $rows;
        };

        $ccTables = [
            [
                'title' => 'CC 1',
                'rows' => $buildCcRows(1, [
                    ['value' => 1, 'label' => "CC 1.1 I know what a CC is and I saw this office's CC."],
                    ['value' => 2, 'label' => "CC 1.2 I know what a CC is but I did not see this office's CC."],
                    ['value' => 3, 'label' => "CC 1.3 I learned of the CC only when I saw this office's CC."],
                    ['value' => 4, 'label' => "CC 1.4 I do not know what a CC is and I did not see this office's CC."],
                ]),
            ],
            [
                'title' => 'CC 2',
                'rows' => $buildCcRows(2, [
                    ['value' => 1, 'label' => 'CC 2.1 Easy to see'],
                    ['value' => 2, 'label' => 'CC 2.2 Somewhat easy to see'],
                    ['value' => 3, 'label' => 'CC 2.3 Difficult to see'],
                    ['value' => 4, 'label' => 'CC 2.4 Not visible at all'],
                    ['value' => 5, 'label' => 'N/A'],
                ]),
            ],
            [
                'title' => 'CC 3',
                'rows' => $buildCcRows(3, [
                    ['value' => 1, 'label' => 'CC 3.1 Helped very much'],
                    ['value' => 2, 'label' => 'CC 3.2 Somewhat helped'],
                    ['value' => 3, 'label' => 'CC 3.3 Did not help'],
                    ['value' => 4, 'label' => 'N/A'],
                ]),
            ],
        ];

        $dimensions = Dimension::all();
        $ratingsQuery = CustomerAttributeRating::select('dimension_id', 'rate_score', DB::raw('count(*) as count'))
            ->whereIn('customer_id', $customerIds);
        if ($startDate && $endDate) {
            $ratingsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $dimensionCounts = $ratingsQuery->groupBy('dimension_id', 'rate_score')
            ->get()
            ->groupBy('dimension_id')
            ->map(function ($group) {
                return $group->keyBy('rate_score');
            });

        $dimensionOrder = [
            'responsiveness' => 'Responsiveness',
            'reliability' => 'Reliability',
            'access-and-facilities' => 'Access and Facilities',
            'communication' => 'Communication',
            'costs' => 'Costs',
            'integrity' => 'Integrity',
            'assurance' => 'Assurance',
            'outcome' => 'Outcome',
            'overall' => 'Overall Satisfaction',
        ];

        $dimensionMap = $dimensions->keyBy(function ($dimension) {
            return strtolower($dimension->slug);
        });

        $sqdRows = [];
        $sqdTotals = [
            'strongly_disagree' => 0,
            'disagree' => 0,
            'neither' => 0,
            'agree' => 0,
            'strongly_agree' => 0,
            'na' => 0,
            'responses' => 0,
        ];

        foreach ($dimensionOrder as $slug => $label) {
            $dimension = $dimensionMap->get($slug);
            if (!$dimension) {
                continue;
            }

            $dimCounts = $dimensionCounts->get($dimension->id, collect());
            $stronglyDisagree = (int) ($dimCounts->get(1)->count ?? 0);
            $disagree = (int) ($dimCounts->get(2)->count ?? 0);
            $neither = (int) ($dimCounts->get(3)->count ?? 0);
            $agree = (int) ($dimCounts->get(4)->count ?? 0);
            $stronglyAgree = (int) ($dimCounts->get(5)->count ?? 0);
            $na = (int) ($dimCounts->get(6)->count ?? 0);
            $responses = $stronglyDisagree + $disagree + $neither + $agree + $stronglyAgree + $na;
            $denominator = $responses - $na;
            $rating = $denominator > 0 ? round((($agree + $stronglyAgree) / $denominator) * 100, 1) : 0;

            $sqdRows[] = [
                'label' => $label,
                'strongly_disagree' => $stronglyDisagree,
                'disagree' => $disagree,
                'neither' => $neither,
                'agree' => $agree,
                'strongly_agree' => $stronglyAgree,
                'na' => $na,
                'responses' => $responses,
                'rating' => $rating,
            ];

            $sqdTotals['strongly_disagree'] += $stronglyDisagree;
            $sqdTotals['disagree'] += $disagree;
            $sqdTotals['neither'] += $neither;
            $sqdTotals['agree'] += $agree;
            $sqdTotals['strongly_agree'] += $stronglyAgree;
            $sqdTotals['na'] += $na;
            $sqdTotals['responses'] += $responses;
        }

        $totalDenominator = $sqdTotals['responses'] - $sqdTotals['na'];
        $sqdTotals['rating'] = $totalDenominator > 0
            ? round((($sqdTotals['agree'] + $sqdTotals['strongly_agree']) / $totalDenominator) * 100, 1)
            : 0;

        $serviceCounts = (clone $csfBase)->select(
                'service_id',
                DB::raw('count(distinct customer_id) as responses'),
                DB::raw('count(*) as transactions')
            )
            ->whereNotNull('service_id')
            ->whereNotNull('customer_id')
            ->groupBy('service_id')
            ->get()
            ->keyBy('service_id');

        $serviceStats = $services->map(function ($service) use ($serviceCounts) {
            $row = $serviceCounts->get($service->id);

            return [
                'id' => $service->id,
                'service_name' => $service->service_name,
                'service_type' => $service->service_type,
                'responses' => (int) ($row->responses ?? 0),
                'transactions' => (int) ($row->transactions ?? 0),
            ];
        });

        $internalServiceStats = $serviceStats
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'internal')
            ->values();

        $externalServiceStats = $serviceStats
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'external')
            ->values();

        $serviceRatings = $services->map(function ($service) use ($csfBase, $startDate, $endDate) {
            $customerIds = (clone $csfBase)
                ->where('service_id', $service->id)
                ->whereNotNull('customer_id')
                ->distinct()
                ->pluck('customer_id');

            if ($customerIds->isEmpty()) {
                return [
                    'id' => $service->id,
                    'service_name' => $service->service_name,
                    'service_type' => $service->service_type,
                    'rating' => null,
                ];
            }

            $baseRatings = CustomerAttributeRating::whereIn('customer_id', $customerIds);
            if ($startDate && $endDate) {
                $baseRatings->whereBetween('created_at', [$startDate, $endDate]);
            }
            $denominator = (clone $baseRatings)
                ->whereIn('rate_score', [1, 2, 3, 4, 5])
                ->count();
            $positive = (clone $baseRatings)
                ->whereIn('rate_score', [4, 5])
                ->count();

            $rating = $denominator > 0 ? round(($positive / $denominator) * 100, 2) : null;

            return [
                'id' => $service->id,
                'service_name' => $service->service_name,
                'service_type' => $service->service_type,
                'rating' => $rating,
            ];
        });

        $internalServiceRatings = $serviceRatings
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'internal')
            ->values();

        $externalServiceRatings = $serviceRatings
            ->filter(fn ($service) => strtolower($service['service_type'] ?? '') === 'external')
            ->values();

        return Inertia::render('Libraries/Divisions/ReportPreview', [
            'data' => [
                'internal_service_stats' => $internalServiceStats,
                'external_service_stats' => $externalServiceStats,
                'client_types' => $clientTypes,
                'age_groups' => $ageGroups,
                'sexes' => $sexes,
                'total_customers' => $totalCustomers,
                'cc_tables' => $ccTables,
                'sqd_rows' => $sqdRows,
                'sqd_totals' => $sqdTotals,
                'internal_service_ratings' => $internalServiceRatings,
                'external_service_ratings' => $externalServiceRatings,
            ],
            'form' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
            'filters' => [
                'period_type' => request()->input('period_type'),
                'selected_month' => request()->input('selected_month'),
                'selected_quarter' => request()->input('selected_quarter'),
                'selected_year' => request()->input('selected_year'),
            ],
        ]);
    })->name('libraries.report');


});
