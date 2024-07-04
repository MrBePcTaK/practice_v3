<?php

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
Auth::routes();
/**
 * Тест контроллер
 */

Route::middleware(['auth'])->group(function () {
    Route::get('/test', 'TestController@index')->name('test');
    Route::get('/test2', 'TestController@index2')->name('test2');

    /** ------------------------------ **/

    /**
     * Роуты главной страницы
     */
    Route::get('/', 'HomeController@index')->name('home');
    Route::post('/users-wait-access', 'UserController@usersWaitAccess')->name('users-wait-access');
    Route::post('/access-change', 'UserController@accessChange')->name('access-change');

    /**
     * Отчеты
     */
    Route::get('/reports', 'ReportController@index')->name('reports-home');
    Route::post('/reports/report-add', 'ReportController@addReport')->name('report-add');
    Route::post('/reports/report-get', 'ReportController@getReport')->name('report-get');
    Route::post('/reports/report-edit', 'ReportController@editReport')->name('report-edit');
    Route::get('/reports/report-generate', 'ReportController@generateGeneralReport')->name('report-generate');

    /**
     * Сотрудники (persons)
     */
    Route::get('/persons', 'UserController@persons')->name('persons-home');
    Route::post('/persons/person-action/{id}', 'UserController@personAction')->name('person-action');
    Route::post('/persons/change-status', 'UserController@changeUserStatus')->name('person-change-status');

    /**
     * Телеграм бот
     */
    Route::get('/telegram', 'TelegramController@index')->name('telegram-home');
});

Route::get('/menu', [\App\Http\Controllers\Api\FoodOrdersController::class, 'index'])->name('menu');

Route::get('/reports/report-generate', 'ReportController@generateGeneralReport')->name('report-generate');
