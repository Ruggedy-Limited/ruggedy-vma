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
Route::get('/home', 'HomeController@index');

Route::get('/workspaces/app/showRecord', 'WorkspaceController@appShowRecord')->name('workspaces.appShowRecord');
Route::get('/workspaces/ruggedyIndex', 'WorkspaceController@ruggedyIndex')->name('workspaces.ruggedyIndex');
Route::get('/workspaces/ruggedyCreate', 'WorkspaceController@ruggedyCreate')->name('workspaces.ruggedyCreate');
Route::get('/workspaces/ruggedyShow', 'WorkspaceController@ruggedyShow')->name('workspaces.ruggedyShow');
