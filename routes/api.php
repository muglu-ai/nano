<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiRelayController;

Route::get('/relay/help-tool/', [ApiRelayController::class, 'enqueueToHelpTool']);
Route::get('/relay/help-tool/{id}', [ApiRelayController::class, 'status']); // optional tracker
