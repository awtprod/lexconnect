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

//Route::get('/', function()
//{
	//User::create([
	//	'username' => 'instauch',
	//	'name' => 'Juda Zigatch',
	//	'password' => Hash::make('sdf1sd2f12s31'),
	//	'company' => 'taint'
	//	]);
//	return User::all();
//});
//Route::get('users', 'UsersController@index');

//Route::get('users/{username}', 'UsersController@show');
Route::get('orders/courts/{id}', 'OrdersController@getCourts');
Route::group(array('before'=>'auth'), function() { 
Route::get('jobs/add', [
	'as' => 'jobs.add',
	'uses' => 'JobsController@add'
	]);
Route::get('tasks/filing', [
	'as' => 'tasks.filing',
	'uses' => 'TasksController@filing'
	]);

Route::get('home/', [
	'as' => 'home.index',
	'uses' => 'HomeController@index'
	]);

});
Route::group(array('before'=>'auth', 'before'=>'csrf'), function() { 
Route::post('search/', [
	'as' => 'search.index',
	'uses' => 'SearchController@index'
	]);
Route::post('jobs/proof', [
	'as' => 'jobs.proof',
	'uses' => 'JobsController@proof'
	]);
Route::post('jobs/add', [
	'as' => 'jobs.add',
	'uses' => 'JobsController@add'
	]);
Route::post('jobs/declaration', [
	'as' => 'jobs.declaration',
	'uses' => 'JobsController@declaration'
	]);
Route::post('tasks/declaration', [
	'as' => 'tasks.declaration',
	'uses' => 'TasksController@declaration'
	]);
Route::post('tasks/create_dec', [
	'as' => 'tasks.create_dec',
	'uses' => 'TasksController@create_dec'
	]);
Route::match(array('GET', 'POST'),'tasks/complete', [
	'as' => 'tasks.complete',
	'uses' => 'TasksController@complete'
	]);
Route::post('tasks/documents', [
	'as' => 'tasks.documents',
	'uses' => 'TasksController@documents'
	]);

Route::post('tasks/proof', [
	'as' => 'tasks.proof',
	'uses' => 'TasksController@proof'
	]);


Route::post('orders/verify', [
	'as' => 'orders.verify',
	'uses' => 'OrdersController@verify'
	]);
Route::post('orders/status', [
	'as' => 'orders.status',
	'uses' => 'OrdersController@status'
	]);
Route::post('jobs/status', [
	'as' => 'jobs.status',
	'uses' => 'JobsController@status'
	]);
Route::get('orders/clear/', [
	'as' => 'orders.clear',
	'uses' => 'OrdersController@clear'
	]);

Route::get('orders/add', [
	'as' => 'orders.add',
	'uses' => 'OrdersController@add'
	]);
Route::any('orders/documents', [
	'as' => 'orders.documents',
	'uses' => 'OrdersController@documents'
	]);
Route::get('orders/revise/', [
	'as' => 'orders.revise',
	'uses' => 'OrdersController@revise'
	]);
Route::post('serve/add', [
	'as' => 'serve.add',
	'uses' => 'ServeController@add'
	]);

});
Route::get('users/forgot_password', [
    'as' => 'forgot_password',
    'uses' => 'UsersController@forgot_password'
]);
Route::match(array('GET', 'POST'),'jobs/create', [
	'as' => 'jobs.create',
	'uses' => 'JobsController@create'
	]);
Route::post('jobs/verify', [
	'as' => 'jobs.verify',
	'uses' => 'JobsController@verify'
	]);
Route::group(array('before'=>'auth'), function() { 
Route::resource('orders', 'OrdersController');
Route::resource('jobs', 'JobsController');
Route::resource('attempts', 'AttemptsController');
Route::resource('tasks', 'TasksController');
Route::resource('serve', 'ServeController');
Route::resource('servee', 'ServeeController');
Route::resource('rules', 'RulesController');
Route::resource('reprojections', 'ReprojectionsController');


});

Route::post('users/push_forgot_password', [
    'as' => 'push_forgot_password',
    'uses' => 'UsersController@push_forgot_password'
]);
Route::get('users/password_reset/{password_reset}', [
    'as' => 'password_reset',
    'uses' => 'UsersController@password_reset'
]);
Route::post('users/post_reset_password/', [
    'as' => 'post_reset_password',
    'uses' => 'UsersController@post_reset_password'
]);
Route::get('users/resend_activation', [
    'as' => 'resend_activation',
    'uses' => 'UsersController@resend_activation'
]);
Route::post('users/new_password/{new_password}', [
    'as' => 'new_password',
    'uses' => 'UsersController@new_password'
]);
Route::post('users/store_edit', [
    'as' => 'users.store_edit',
    'uses' => 'UsersController@store_edit'
]);
Route::resource('users', 'UsersController');
Route::get('login', [
	'as' => 'login',
	'uses' => 'SessionsController@create'
	]);
Route::get('logout', 'SessionsController@destroy');
Route::resource('sessions', 'SessionsController');
Route::group(array('before'=>'auth'), function() { 
Route::resource('company', 'CompanyController');
});
Route::get('users/activation/{activation_code}', [
    'as' => 'activation_path',
    'uses' => 'UsersController@activation'
]);

Route::post('users/push_resend_activation', [
    'as' => 'push_resend_activation',
    'uses' => 'UsersController@push_resend_activation'
]);
Route::get('orders/files/{file}', [
	'as' => 'orders.files',
	'uses' => 'OrdersController@files'
	]);
Route::get('/', [
	'as' => 'home.redirect',
	'uses' => 'HomeController@redirect'
	]);

