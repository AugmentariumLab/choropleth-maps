<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BayAreaController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DatasetManagerController;

Route::get('/', [IndexController::class, 'index']);
Route::get('/bayarea/query_by_zip', [BayAreaController::class, "query_by_zip"]);
Route::get('/bayarea/benchmark', [BayAreaController::class, "benchmark"]);
Route::get('/phpinfo', function() {
  phpinfo();
});
Auth::routes(['register' => false]);
Route::get('/datasets', [DatasetManagerController::class, 'index']);

Route::post('/api/dataset', [DatasetController::class, 'store']);
Route::delete('/api/dataset', [DatasetController::class, 'destroy']);
