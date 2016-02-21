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
Route::get('api/getcourts', 'OrdersController@getCourts');
Route::get('api/getcounties', 'CountiesController@getCounties');
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
    Route::get('vendorRates/', [
        'as' => 'vendorrates.index',
        'uses' => 'VendorRatesController@index'
    ]);
	Route::get('vendorRates/destroy/{id}', [
			'as' => 'vendorrates.destroy',
			'uses' => 'VendorRatesController@destroy'
	]);
    Route::get('orders/edit/{id}', [
        'as' => 'orders.edit',
        'uses' => 'OrdersController@edit'
    ]);
    Route::get('steps/edit/{id}', [
        'as' => 'steps.edit',
        'uses' => 'StepsController@edit'
    ]);
	Route::get('steps/', [
			'as' => 'steps.index',
			'uses' => 'StepsController@index'
	]);
    Route::get('steps/create/', [
        'as' => 'steps.create',
        'uses' => 'StepsController@create'
    ]);
	Route::get('steps/destroy/{id}', [
			'as' => 'steps.destroy',
			'uses' => 'StepsController@destroy'
	]);
    Route::get('processes/edit/{id}', [
        'as' => 'processes.edit',
        'uses' => 'ProcessesController@edit'
    ]);
	Route::get('processes/destroy/{id}', [
			'as' => 'processes.destroy',
			'uses' => 'ProcessesController@destroy'
	]);
    Route::get('dependent/process/{id}', [
        'as' => 'dependent.edit',
        'uses' => 'DependentController@edit'
    ]);
	Route::get('template/edit/{id}', [
			'as' => 'template.edit',
			'uses' => 'TemplateController@edit'
	]);
	Route::get('template/', [
			'as' => 'template.index',
			'uses' => 'TemplateController@index'
	]);
	Route::get('template/destroy/{id}', [
			'as' => 'template.destroy',
			'uses' => 'TemplateController@destroy'
	]);
	Route::match(array('GET', 'POST'),'counties/', [
			'as' => 'counties.index',
			'uses' => 'CountiesController@index'
	]);
	Route::match(array('GET', 'POST'),'clientRates/', [
			'as' => 'clientRates.index',
			'uses' => 'ClientRatesController@index'
	]);
});
Route::group(array('before'=>'auth', 'before'=>'csrf'), function() {
	Route::post('vendorRates/store', [
			'as' => 'vendorrates.store',
			'uses' => 'VendorRatesController@store'
	]);
    Route::post('steps/store', [
        'as' => 'steps.store',
        'uses' => 'StepsController@store'
    ]);
    Route::post('steps/update', [
        'as' => 'steps.update',
        'uses' => 'StepsController@update'
    ]);
    Route::get('steps/edit/{id}', [
        'as' => 'steps.edit',
        'uses' => 'StepsController@edit'
    ]);
	Route::post('template/store', [
			'as' => 'template.store',
			'uses' => 'TemplateController@store'
	]);
	Route::post('template/update', [
			'as' => 'template.update',
			'uses' => 'TemplateController@update'
	]);
	Route::post('template/add', [
			'as' => 'template.add',
			'uses' => 'TemplateController@add'
	]);
    Route::post('clientRates/update', [
        'as' => 'clientRates.update',
        'uses' => 'ClientRatesController@update'
    ]);
    Route::get('documents/upload', [
        'as' => 'documents.upload',
        'uses' => 'DocumentsController@upload'
    ]);
    Route::get('documents/view', [
        'as' => 'documents.view',
        'uses' => 'DocumentsController@view'
    ]);
	Route::post('documents/filedDocuments', [
			'as' => 'documents.filedDocuments',
			'uses' => 'DocumentsController@filedDocuments'
	]);
    Route::post('documents/storeDocuments', [
        'as' => 'documents.storeDocuments',
        'uses' => 'DocumentsController@storeDocuments'
    ]);
    Route::post('documents/storeFiledDocuments', [
        'as' => 'documents.storeFiledDocuments',
        'uses' => 'DocumentsController@storeFiledDocuments'
    ]);
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
Route::post('jobs/actions', [
	'as' => 'jobs.actions',
	'uses' => 'JobsController@actions'
	]);
Route::post('tasks/accept', [
			'as' => 'tasks.accept',
			'uses' => 'TasksController@accept'
	]);
Route::post('tasks/attempt', [
			'as' => 'tasks.attempt',
			'uses' => 'TasksController@attempt'
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

Route::post('tasks/upload', [
			'as' => 'tasks.upload',
			'uses' => 'TasksController@upload'
	]);

    Route::post('tasks/proofFiled', [
        'as' => 'tasks.proofFiled',
        'uses' => 'TasksController@proofFiled'
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
    Route::post('orders/update', [
        'as' => 'orders.update',
        'uses' => 'OrdersController@update'
    ]);
    Route::post('processes/update', [
        'as' => 'processes.update',
        'uses' => 'ProcessesController@update'
    ]);
Route::post('serve/add', [
	'as' => 'serve.add',
	'uses' => 'ServeController@add'
	]);
Route::post('counties/update', [
			'as' => 'counties.update',
			'uses' => 'CountiesController@update'
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
Route::resource('documents', 'DocumentsController');
Route::resource('processes', 'ProcessesController');
Route::resource('dependent', 'DependentController');




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

