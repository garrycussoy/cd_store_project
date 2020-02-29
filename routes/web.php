<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/* ---------- Category Route ---------- */
/* Get all available categories */
Route::get('/category','CategoryController@get');
/* Add new category */
Route::post('/category','CategoryController@post');
/* Edit a category specified by its ID */
Route::put('/category/{id}','CategoryController@put');
/* Soft delete a category specified by its ID */
Route::delete('/category/{id}','CategoryController@softDelete');

/* ---------- CD Route ---------- */
/* Get all available CD's */
Route::get('/cd','CdController@get');
/* Insert new CD */
Route::post('/cd','CdController@post');
/* Edit information of a CD */
Route::put('/cd/{id}','CdController@put');
/* Soft delete a CD */
Route::delete('/cd/{id}','CdController@softDelete');
/* Get information of a CD based on CD ID */
Route::get('/cd/{id}','CdController@getById');

/* ---------- User Route ---------- */
/* Get all registered users who have not been banned */
Route::get('/user','UserController@get');
/* Add new user */
Route::post('/user','UserController@post');
/* Edit information of a user */
Route::put('/user/{id}','UserController@put');
/* Banned a user (soft delete) */
Route::delete('/user/{id}','UserController@softDelete');
/* Get information of a user based on user ID */
Route::get('/user/{id}','UserController@getById');

/* ---------- Rent Route ---------- */
/* Get all transactions */
Route::get('/rent','RentController@get');
/* Start a transaction */
Route::post('/rent','RentController@startRent');
/* End a transaction (when user return the books he/she borrowed) */
Route::put('/rent/{id}','RentController@endRent');
/* Get transaction detail based on Rent ID */
Route::get('/rent/{id}','RentController@getById');
