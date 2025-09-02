<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:30,1')
    ->group(function () {
        Route::get('/stats', [TicketController::class, 'stats']);
        Route::post('/tickets/{ticket}/classify', [TicketController::class, 'classify']);
        Route::apiResource('tickets', TicketController::class);
    });
