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

Route::get('/theme', 'HomeController@theme'); //Temporary route to theme.

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index');

Route::get('/settings/', 'SettingsController@index')->name('settings.index');
Route::get('/settings/users/create', 'SettingsController@userCreate')->name('settings.users.create');
Route::get('/settings/users/edit', 'SettingsController@userEdit')->name('settings.users.edit');
Route::get('/settings/users/profile', 'SettingsController@userProfile')->name('settings.users.profile');

Route::get('/workspaces/app', 'WorkspaceController@app')->name('workspaces.app');
Route::get('/workspaces/app/show', 'WorkspaceController@appShow')->name('workspaces.appShow');
Route::get('/workspaces/app/showRecord', 'WorkspaceController@appShowRecord')->name('workspaces.appShowRecord');
Route::get('/workspaces/addFile', 'WorkspaceController@addFile')->name('workspaces.addFile');
Route::get('/workspaces/apps', 'WorkspaceController@apps')->name('workspaces.apps');
Route::get('/workspaces/apps/create', 'WorkspaceController@appsCreate')->name('workspaces.apps.create');
Route::get('/workspaces/', 'WorkspaceController@index')->name('workspaces.index');
Route::get('/workspaces/create', 'WorkspaceController@create')->name('workspaces.create');
Route::get('/workspaces/ruggedyIndex', 'WorkspaceController@ruggedyIndex')->name('workspaces.ruggedyIndex');
Route::get('/workspaces/ruggedyCreate', 'WorkspaceController@ruggedyCreate')->name('workspaces.ruggedyCreate');
Route::get('/workspaces/ruggedyShow', 'WorkspaceController@ruggedyShow')->name('workspaces.ruggedyShow');

Route::get('/folders/index', 'FolderController@index')->name('folders.index');
Route::get('/folders/create', 'FolderController@create')->name('folders.create');
Route::get('/folders/show', 'FolderController@show')->name('folders.show');
