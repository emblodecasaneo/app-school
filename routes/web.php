<?php

use App\Http\Controllers\AttributtionController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\StudentController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('niveaux')->group(function(){
        Route::get('/', [LevelController::class, 'index'])->name('niveaux');
        Route::get('/create_level', [LevelController::class, 'create'])->name('niveaux.create_level');
        Route::get('/update_level/{level}', [LevelController::class, 'edit'])->name('niveaux.update_level');
    });

    Route::prefix('classes')->group(function(){
        Route::get('/', [ClasseController::class, 'index'])->name('classes');
        Route::get('/create_classe', [ClasseController::class, 'create'])->name('classes.create_level');
        Route::get('/update_classe/{classe}', [ClasseController::class, 'edit'])->name('classes.update_classe');
    });


    Route::prefix('students')->group(function(){
        Route::get('/', [StudentController::class, 'index'])->name('students');
        Route::get('/create_student', [StudentController::class, 'create'])->name('students.create_student');
        Route::get('/update_student/{student}', [StudentController::class, 'edit'])->name('students.update_student');
    });


    Route::prefix('inscriptions')->group(function(){
        Route::get('/', [AttributtionController::class, 'index'])->name('inscriptions');
        Route::get('/create_inscription', [AttributtionController::class, 'create'])->name('inscriptions.create_inscription');
        Route::get('/update_inscription/{attributtion}', [AttributtionController::class, 'edit'])->name('inscriptions.update_inscription');

    });


    Route::prefix('paiements')->group(function(){
        Route::get('/', [PaymentController::class, 'index'])->name('paiements');
        Route::get('/create_paiement', [PaymentController::class, 'create'])->name('paiements.create_paiement');
        Route::get('/update_paiement/{paiements}', [PaymentController::class, 'edit'])->name('paiements.update_paiements');

    });

    Route::prefix('settings')->group(function(){
        Route::get('/', [SchoolYearController::class, 'index'])->name('settings');
        Route::get('/create_school_year', [SchoolYearController::class, 'create'])->name('settings.create_schoolyear');
    });
});
