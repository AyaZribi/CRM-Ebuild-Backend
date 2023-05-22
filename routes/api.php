<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TacheController;
use App\Models\Facture;
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
Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'forgot']);
Route::post('reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('changePassword', [AuthController::class, 'ChangePassword']);
    ////////////////////////personnel////////////////////////

    Route::post('/personnel', [AuthController::class, 'store1']);
    Route::get('/personnel',  [AuthController::class, 'index']);
    Route::delete('/personnel/{id}',  [AuthController::class, 'destroy']);
    Route::put('/personnel/{id}',  [AuthController::class, 'updatel']);
    ////////////////////////client////////////////////////

    Route::post('/clients', [ClientController::class, 'storeclient']);
    Route::put('/clients/{id}', [ClientController::class, 'updatec']);
    Route::delete('/clients/{id}', [ClientController::class, 'deletec']);
    Route::get('/clients', [ClientController::class, 'viewallc']);
    ////////////////////////facture////////////////////////

    Route::post('factures/add', [FactureController::class, 'store']);
    Route::post('/factures/send', [FactureController::class, 'sendPdfToClient']);
    Route::get('/sendpdf/{facture}', function(Facture $facture) {
        $controller = new FactureController(); // Replace with your actual controller name
        $controller->sendPdfCopyToClient($facture);

        return redirect()->back()->with('success', 'PDF copy sent to client successfully!');
    })->name('sendPdfCopyToClient');

    Route::put('/facture/{id}', [FactureController::class, 'update']);
    Route::delete('/facture/{id}', [FactureController::class, 'destroy']);
    Route::get('/facture/{id}', [FactureController::class, 'show']);
    Route::get('/factures', [DevisController::class, 'showall']);
    Route::get('/factures/{facture}/pdf', [FactureController::class, 'generatePdf']);
    //Route::get('/factures/{facture}/pdf', [FactureController::class, 'sendPdfToClient']);
    ////////////////////////devis////////////////////////

    Route::apiResource('devis', DevisController::class);
    Route::put('/devis/{id}', [DevisController::class, 'update']);
    Route::delete('/devis/{id}', [DevisController::class, 'destroy']);
    Route::get('/devis/{id}', [DevisController::class, 'show']);
    Route::get('/devis', [DevisController::class, 'showall']);
    Route::get('devis/{id}/pdf', [DevisController::class, 'generate']);
     ////////////////////////project////////////////////////

    Route::post('project/add', [ProjectController::class, 'store']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::get('/projects', [ProjectController::class, 'showAll']);

    Route::post('/tickets', [ProjectController::class, 'storeTicket'])->name('tickets.store');

    // Route for showing a ticket
    Route::get('/tickets/{id}', [ProjectController::class, 'showTicket'])->name('tickets.show');
    Route::get('/ticket/client', [ProjectController::class, 'showClientTickets']);
    Route::get('/ticket/personnel', [ProjectController::class, 'viewAssignedTickets']);
    Route::get('/alltickets', [ProjectController::class, 'getAllTickets']);





// Route for answering a ticket
    Route::post('/tickets/{id}/answer', [ProjectController::class, 'answerTicket'])->name('tickets.answer');
});
////////////////////////tache////////////////////////

Route::post('/tache', [TacheController::class, 'store']);
Route::put('/taches/{tache}',[TacheController::class, 'update']);
Route::delete('/taches/{tache}',[TacheController::class, 'destroy']);
Route::get('/taches/{tache}',[TacheController::class, 'show']);
Route::post('/taches/{tache}/comments', [TacheController::class, 'createcomment']);






/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
