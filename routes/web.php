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
