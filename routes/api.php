<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PdfController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::put('/user/password', 'App\Http\Controllers\AuthController@updatePassword')->middleware('auth');
Route::view('reset-password/{token}', 'auth.reset-password')->name('password.reset');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('changePassword', [AuthController::class, 'ChangePassword']);

    Route::post('/personnel', [AuthController::class, 'store1']);

// View all personnel
    Route::get('/personnel',  [AuthController::class, 'index']);

    // Delete a personnel
    Route::delete('/personnel/{id}',  [AuthController::class, 'destroy']);

    // Update a personnel
    Route::put('/personnel/{id}',  [AuthController::class, 'updatel']);

    Route::post('/clients', [ClientController::class, 'storeclient']);
    Route::put('/clients/{id}', [ClientController::class, 'updatec']);
    Route::delete('/clients/{id}', [ClientController::class, 'deletec']);
    Route::get('/clients', [ClientController::class, 'viewallc']);


});
Route::apiResource('devis', DevisController::class);
Route::put('/devis/{id}', [DevisController::class, 'update']);
Route::delete('/devis/{id}', [DevisController::class, 'destroy']);
Route::get('/devis/{id}', [DevisController::class, 'show']);
Route::get('/devis', [DevisController::class, 'showall']);
Route::get('devis/{id}/pdf', [DevisController::class, 'generate']);

Route::post('factures/add', [FactureController::class, 'store']);
Route::put('/facture/{id}', [FactureController::class, 'update']);
Route::delete('/facture/{id}', [FactureController::class, 'destroy']);
Route::get('/facture/{id}', [FactureController::class, 'show']);
Route::get('/factures/{facture}/pdf', [FactureController::class, 'generatePdf']);




Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'forgot']);
Route::post('reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset']);
/*Route::post('sendPasswordResetLink', 'App\Http\Controllers\PasswordResetRequestController@sendEmail');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');*/


/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
