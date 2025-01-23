<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Filament\Pages\StockCritico;
use App\Http\Controllers\PdfController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('livewire/pdf', [App\Livewire\Entradas::class, 'pdf'])
->name('livewire/pdf');

Route::get('/stock-critico', StockCritico::class)->name('stock-critico');

Route::get('/download-pdf', [PdfController::class, 'downloadPdf'])->name('download-pdf');