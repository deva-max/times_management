<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    
    Route::get('report', [ReportController::class, 'create'])->name('report');
    Route::post('report/generate', [ReportController::class, 'generateReport'])->name('reports.generate');
    Route::post('report/download', [ReportController::class, 'downloadCSV'])->name('reports.downloadCSV');
    
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
