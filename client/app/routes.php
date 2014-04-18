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
	return Redirect::action('CollectionsController@getIndex');
});
Route::get('/cover/{isbn}', 'DocumentsController@locateCover');

#Route::controller('/cover', 'CoversController');

# Actions available without login
//Route::get('/collections/{id}', 'CollectionsController@getShow');
/*
Route::get('collections/{collectionId}/documents', array(
	'as' => 'collectionDocuments',
	'uses' => 'CollectionsController@getShow'
));
*/

// No need for auth:
Route::get('/documents', 'DocumentsController@getIndex');
Route::get('/documents/show/{documentId}', 'DocumentsController@getShow');
Route::get('/collections', 'CollectionsController@getIndex');

Route::get('/selfservice', function() {
	return View::make('selfservice');
});

Route::get('/minelan', function() {
	return View::make('minelaan');
});

// TODO: Trekke ut det som trenger auth
Route::controller('users', 'UsersController');


Route::group(array('before' => 'guest'), function()
{
	Route::get('/librarians/login', 'LibrariansController@getLogin');
	Route::post('/librarians/login', 'LibrariansController@postLogin');
	Route::get('/librarians/activate/{token}', 'LibrariansController@getActivate');
	Route::post('/librarians/activate', 'LibrariansController@postActivate');
});

Route::group(array('before' => 'auth'), function()
{

	Route::get('/documents/edit/{id}', 'DocumentsController@getEdit');

	//Route::get('/collections/{collectionId}/documents/{documentId}/remove', 'CollectionsController@getRemoveDocument');
	Route::controller('collections', 'CollectionsController');

	Route::get('/librarians/edit', 'LibrariansController@getEdit');
	Route::post('/librarians/edit', 'LibrariansController@postStore');
	Route::get('/librarians/logout', 'LibrariansController@getLogout');
//	Route::get('/librarians', 'LibrariansController@getIndex');

	//Route::controller('documents', 'DocumentsController');
	Route::controller('librarians', 'LibrariansController');

	Route::controller('loans', 'LoansController');
	Route::controller('reminders', 'RemindersController');

	Route::get('/documents/edit/{documentId}', 'DocumentsController@getEdit');
	Route::put('/documents/{documentId}', 'DocumentsController@putUpdate');
	Route::post('/documents/store/{documentId}', 'DocumentsController@postStore');

});

