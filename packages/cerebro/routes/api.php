<?php

use App\Http\Controllers\Api\v1\CreditCardController;
use App\Models\CustomerState;
use App\Services\AbaService;
use App\Services\DnmService;
use App\Services\LocationService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ReapplyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\v1\ErrorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('v1/user', function (Request $request) {
    return $request->user();
});

Route::group((['middleware' => ['auth:sanctum'], 'prefix' => 'v1']), function() {
    Route::get('/search_main', [ReapplyController::class, 'search_main'])->middleware('ssn.request');
    Route::post('/search_reapply', [ReapplyController::class, 'search_reapply'])->middleware(['ssn.check', 'search_reapply']);
    Route::get('/get_reapply', [ReapplyController::class, 'get_reapply']);
    Route::post('/check_phone', [ReapplyController::class, 'check_phone']);
    Route::post('/get_started', [ReapplyController::class, 'get_started'])->middleware('ssn.request');
    Route::post('/update_main', [ReapplyController::class, 'update_main'])->middleware('ssn.request');
    Route::post('/update_reapply', [ReapplyController::class, 'update_reapply'])->middleware('ssn.request');
    Route::post('/form', [ReapplyController::class, 'store'])->middleware('ssn.check');
    Route::post('/reapply/import', [ReapplyController::class, 'import']);
    Route::post('/unsubscribe', [ReapplyController::class, 'unsubscribe']);
    Route::get('/check_connection', [ReapplyController::class, 'check_connection']);
    Route::post('/check_status', [ReapplyController::class, 'check_status']);
    Route::post('/check_status_decision', [ReapplyController::class, 'check_status_decision']);
    Route::get('/check_decision', [ReapplyController::class, 'check_decision']);
    Route::post('/save_error', [ErrorController::class, 'save']);
    Route::post('/save_customer_state', [ReapplyController::class, 'save_customer_state']);

    Route::post('/validation_update_main', [ReapplyController::class, 'validation_update_main']);
    Route::post('/validation_update_reapply', [ReapplyController::class, 'validation_update_reapply']);
    Route::post('/validation_form', [ReapplyController::class, 'validation_store']);

    Route::get('/mark_redirected', [ReapplyController::class, 'mark_redirected']);
    Route::get('/mark_denied', [ReapplyController::class, 'mark_denied']);

    Route::post('/credit_card', [CreditCardController::class, 'store']);
    Route::get('/credit_card', [CreditCardController::class, 'get_card_items']);
    Route::post('/credit_card/count_visitor', [CreditCardController::class, 'visitorCardHandler']);
    Route::post('/credit_card/count_click', [CreditCardController::class, 'clickCardHandler']);
    Route::post('/credit_card/unsubscribe', [CreditCardController::class, 'unsubscribe']);

    Route::post('/log_api_time', [LogService::class, 'saveLog']);
    Route::post('/log_api_time/{id}', [LogService::class, 'updateLog']);

    Route::post('/get_click_id', [DnmService::class, 'getClickIdRequest']);
    Route::post('/action_type_visitor', [ReapplyController::class, 'change_action_type_visitor']);

    Route::group((['middleware' => ['admin']]), function () {
        Route::post('/logoutall', [AuthController::class, 'logoutAll']);
        Route::post('/logout/{id}', [AuthController::class, 'logout']);
        Route::apiResource('reapply', ReapplyController::class, ['except' => ['store','update']]);
    });
    Route::middleware('visitor')->get('count_visitor', function(Request $request) {
        return $request->visit_id;
    });
    Route::middleware('click')->get('count_click', function() {
        return true;
    });
    Route::middleware('save_step_visitor')->post('save_step', function() {
        return true;
    });
});

Route::get('v1/recall', [CustomerState::class, 'get_customer_state']);
Route::get('v1/check_timeout', function() {
    sleep(300);
    return 1;
});

Route::get('v1/aba', function(Request $request) {
    return AbaService::sendRequest($request->input('rn'));
});

Route::get('v1/location', function(Request $request) {
    return LocationService::sendRequest($request->input('address'),$request->input('zip'));
});

Route::post('v1/reapply/send', [ReapplyController::class, 'send']);

Route::get('v1/get_captcha', [ReapplyController::class, 'get_captcha']);

