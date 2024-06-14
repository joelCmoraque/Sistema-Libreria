<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

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

Route::get('livewire.pdf', [ReportController::class,'Registros'])->name('livewire.pdf');
