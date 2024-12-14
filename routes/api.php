<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/deploy/manual/{token}', [App\Http\Controllers\DeployerController::class, 'manualDeploy'])->name('deploy.manual');    
Route::post('/deploy/webhook/{token}', [App\Http\Controllers\DeployerController::class, 'manualDeploy'])->name('deploy.manual');    
