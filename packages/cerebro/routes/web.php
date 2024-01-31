<?php

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ServersController;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\Google2faController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

Route::get('health', function () {
    header('GITHUB_SHA:'.env('GITHUB_SHA'));
    return response()->json(true);
});
Auth::routes(['verify' => true, 'register' => false]);

Route::get('set-cookies', function (\Illuminate\Http\Request $request) {
    Cookie::queue(Cookie::make('shared_email', $request->value, 60 * 24 * 90, null, null, true, false, false, 'None'));
    return response()->json(1);
});

Route::get('get-cookies', function (\Illuminate\Http\Request $request) {
    return response()
        ->json(Cookie::get('shared_email'))
        ->withCallback($request->input('callback'));
});

Route::middleware(['auth'])->group(function () {

    Route::post('/enable2fa', [Google2faController::class, 'enable2fa'])->name('enable2fa');
    Route::get('/generate2fa', [Google2faController::class, 'generateGoogle2fa'])->name('generate2fa');

    Route::middleware(['google.2fa'])->group(function () {

        Route::post('/validate2fa', function () {
            return redirect('home');
        })->name('validate2fa');

        Route::middleware(['role:user'])->group(function () {
            Route::get('/', [HomeController::class, 'index']);
            Route::get('/home', [HomeController::class, 'index']);
            Route::get('/dashboard', [HomeController::class, 'dashboard']);
            Route::get('/cards', [HomeController::class, 'cards']);
            Route::get('/get_cards', [HomeController::class, 'get_cards']);
            Route::get('/dashboard-chart', [HomeController::class, 'dashboardChart']);
            Route::get('/statistic', [StatisticController::class, 'index']);

            Route::resource('sites-crud', SitesController::class)->except(['create', 'update']);
            Route::resource('cards-crud', CardsController::class)->except(['create', 'update']);
            Route::resource('servers-crud', ServersController::class)->except(['create', 'update']);
            Route::post('servers-crud/{id}', [ServersController::class, 'update']);
            // Route::post('sites-crud/{id}', [SitesController::class, 'update']);
            Route::post('cards-crud/{id}', [CardsController::class, 'update']);

            Route::post('/users/{id}/regenerate', [UsersController::class, 'regenerate'])
                ->middleware('verified');

            Route::resource('users', UsersController::class)->except(['create']);
            Route::get('/profile', function () {
                return view('dashboard.profile');
            });

            Route::post('/main-statistic', [StatisticController::class, 'mainStatisticDatatables']);
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::post('/sites/get-settings', [SitesController::class, 'getThemeSettings']);
            Route::post('/sites/merge-settings', [SitesController::class, 'mergeSiteSettings']);
            // Route::post('/sites/update-site', [SitesController::class, 'saveSite']);
            // Route::post('/sites/save-site', [SitesController::class, 'saveSite']);
            Route::get('/forms', function () {
                // return Inertia::render('Forms');
                return view('dashboard.users');
            });
            Route::get('/sites', [SitesController::class, 'sites'])->name('site-index');
            Route::get('/sites/create', [SitesController::class, 'create'])->name('create.site');
            Route::get('/sites/{id}', [SitesController::class, 'edit'])->name('edit.site');
            Route::post('/sites/{id}', [SitesController::class, 'requestCert'])->name('sites');

            Route::get('/servers', [ServersController::class, 'servers']);

            Route::post('/cname/{id}', [SitesController::class, 'getCname'])->name('cname');
            Route::post('/email/resend/{id}', [UsersController::class, 'resendEmailConfirmation']);


            // ---------------------------------------------
            Route::get('/switch-interface', function () {
                session(['use_inertia' => !session('use_inertia', false)]);
                return session('use_inertia');
            })->name('switch-interface');

            Route::get('/migrate', [TestController::class, 'migrate']);
            Route::get('/clear-theme-settings', [TestController::class, 'clearThemeSettings']);
        });
    });
});
