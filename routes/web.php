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

/* ~/api/ はnodeで使っているため、使わない */

Route::get('/', function () {
    return view('topmenu');
})->name('top')->middleware('auth');

Route::middleware('lentex')->group(function () {

    Route::get('/entex/lrs','App\Http\Controllers\EntexController@selectLRs')->name('entex-lrs');
    Route::get('/entex/students','App\Http\Controllers\EntexController@selectStudents')->name('entex-students');
    Route::post('/entex/confirm','App\Http\Controllers\EntexController@confirm')->name('entex-confirm');
    Route::post('/entex/enter','App\Http\Controllers\EntexController@enter');
    Route::post('/entex/exit','App\Http\Controllers\EntexController@exit');
    //livewireを直接呼び出す方がよいとわかったため変更
    // Route::get('/entex/history','App\Http\Controllers\EntexController@indexEntexHistory')->name('entex-history');
    Route::get('/entex/history',App\Http\Livewire\EntexHistory::class)->name('entex-history');

    Route::get('/student/add/', 'App\Http\Controllers\StudentController@add')->name('student-add');
    Route::post('/student/create/', 'App\Http\Controllers\StudentController@create');
    Route::post('/student/edit/', 'App\Http\Controllers\StudentController@edit');
    Route::post('/student/delete/', 'App\Http\Controllers\StudentController@delete');

    Route::get('/lineuser/index/', 'App\Http\Controllers\LineUserController@index')->name('lineu-index');
    Route::get('/lineuser/delete/', 'App\Http\Controllers\LineUserController@delete');

    Route::get('/userauth/add/', 'App\Http\Controllers\UserAuthController@add')->name('userAuth-add');
    Route::post('/userauth/create/', 'App\Http\Controllers\UserAuthController@create');
    Route::get('/userauth/delete/', 'App\Http\Controllers\UserAuthController@delete');

    //v2.0
    Route::get('/supermenu/',function() { return view('supermenu'); })->name('supermenu');
    Route::get('/sessions/',\App\Http\Livewire\SessionCreate::class)->name('sessions.create');
    Route::get('/sessions/{session_id}/attendance', \App\Http\Livewire\SessionAttendanceEdit::class)->name('sessions.attend-edit');


});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
