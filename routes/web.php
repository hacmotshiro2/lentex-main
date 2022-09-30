<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* ~/api はnodeで使っているため、使わない */

Route::get('/', function () {
    return view('topmenu');
})->name('top')->middleware('auth');

Route::middleware('lentex')->group(function () {

    Route::get('/student/add/', 'App\Http\Controllers\StudentController@add')->name('student-add');
    Route::post('/student/add/', 'App\Http\Controllers\StudentController@create');
    Route::post('/student/edit/', 'App\Http\Controllers\StudentController@edit');
    Route::post('/student/delete/', 'App\Http\Controllers\StudentController@delete');

    Route::get('/lineuser/index/', 'App\Http\Controllers\LineUserController@index')->name('lineu-index');
    Route::post('/lineuser/delete/', 'App\Http\Controllers\LineUserController@delete');

    Route::get('/userauth/add/', 'App\Http\Controllers\LineUserController@index')->name('userAuth-regist');

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
