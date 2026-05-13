<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Base\Controllers\Apis\Auth\AuthController;

use App\Modules\Base\Controllers\Factory\HandleController;
use App\Services\FmcsaService;
Route::get('/test-fmcsa/{docket}', function ($docket, FmcsaService $fmcsaService) {

    $response = $fmcsaService->getCarrierByDocketNumber($docket);

    return response()->json($response);
});
Route::post('/backend/auth/login', [AuthController::class, 'login']);
Route::post('/backend/auth/signup', [AuthController::class, 'signup']);
Route::post('/handle/{any}', [HandleController::class, 'handler'])->where('any', '.*');
