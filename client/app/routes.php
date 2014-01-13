<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function() {
	return Redirect::action('DocumentsController@getIndex');
});
Route::get('/cover/{isbn}', 'DocumentsController@locateCover');


Route::get('/selfservice', function() {
	return View::make('selfservice');
});

Route::get('/minelan', function() {
	return View::make('minelaan');
});

#Route::controller('/cover', 'CoversController');

Route::controller('documents', 'DocumentsController');
Route::get('collections/{collectionId}/documents', array('as' => 'collectionDocuments', 'uses' => 'DocumentsController@getIndex'));

Route::controller('collections', 'CollectionsController');
Route::controller('loans', 'LoansController');
Route::controller('users', 'UsersController');


