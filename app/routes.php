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
Route::get('api/getRate', 'JobsController@getRate');
Route::get('orders/courts/{id}', 'OrdersController@getCourts');
Route::group(array('before'=>'auth'), function() {
Route::get('api/tasksTable', 'TasksController@tasksTable');
Route::get('api/jobsTable', 'TasksController@jobsTable');
Route::get('attempts/view', [
		'as' => 'attempts.view',
		'uses' => 'AttemptsController@view'
	]);
Route::get('earnings/', [
		'as' => 'invoices.earnings',
		'uses' => 'InvoicesController@earnings'
	]);
Route::get('payments/', [
		'as' => 'invoices.payments',
		'uses' => 'InvoicesController@payments'
	]);
Route::get('pay/', [
		'as' => 'invoices.pay',
		'uses' => 'InvoicesController@pay'
	]);
Route::get('bill/', [
		'as' => 'invoices.bill',
		'uses' => 'InvoicesController@bill'
	]);
Route::get('states/', [
		'as' => 'states.index',
		'uses' => 'StatesController@index'
	]);
Route::get('states/test', [
		'as' => 'states.test',
		'uses' => 'StatesController@test'
	]);
Route::get('jobs/add', [
	'as' => 'jobs.add',
	'uses' => 'JobsController@add'
	]);
Route::get('home/', [
	'as' => 'home.index',
	'uses' => 'HomeController@index'
	]);
	Route::match(array('GET', 'POST'),'rates/vendor/', [
        'as' => 'vendorrates.index',
        'uses' => 'VendorRatesController@index'
    ]);
	Route::get('rates/Vendor/{id}', [
		'as' => 'vendorrates.show',
		'uses' => 'VendorRatesController@show'
	]);
	Route::get('rates/Client/{id}', [
		'as' => 'clientrates.show',
		'uses' => 'ClientRatesController@show'
	]);
	Route::get('rates/vendor/destroy/{id}', [
			'as' => 'vendorrates.destroy',
			'uses' => 'VendorRatesController@destroy'
	]);
	Route::get('locations/', [
		'as' => 'locations.index',
		'uses' => 'LocationsController@index'
	]);
	Route::get('locations/destroy/{id}', [
		'as' => 'locations.destroy',
		'uses' => 'LocationsController@destroy'
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
	Route::match(array('GET', 'POST'),'rates/client/', [
			'as' => 'clientRates.index',
			'uses' => 'ClientRatesController@index'
	]);
	Route::get('documents/view/', [
		'as' => 'documents.view',
		'uses' => 'DocumentsController@view'
	]);
	Route::get('documents/show/{id}', [
		'as' => 'documents.show',
		'uses' => 'DocumentsController@show'
	]);
	Route::get('documents/upload/{id}', [
		'as' => 'documents.upload',
		'uses' => 'DocumentsController@upload'
	]);
	Route::get('company/', [
		'as' => 'company.index',
		'uses' => 'CompanyController@index'
	]);
	Route::get('users/delete/{id}', [
		'as' => 'user.delete',
		'uses' => 'UsersController@delete'
	]);
	Route::post('tasks/destroy', [
		'as' => 'tasks.destroy',
		'uses' => 'TasksController@destroy'
	]);

	Route::post('tasks/complete', [
		'as' => 'tasks.complete',
		'uses' => 'TasksController@complete'
	]);
	Route::post('tasks/finish', [
		'as' => 'tasks.finish',
		'uses' => 'TasksController@finish'
	]);
	Route::post('tasks/clear', [
		'as' => 'tasks.clear',
		'uses' => 'TasksController@clear'
	]);
	Route::get('tasks/filing', [
		'as' => 'tasks.filing',
		'uses' => 'TasksController@filing'
	]);
	Route::get('tasks/', [
		'as' => 'tasks.index',
		'uses' => 'TasksController@index'
	]);
	Route::get('tasks/service_documents/{id}', [
		'as' => 'tasks.service_documents',
		'uses' => 'TasksController@service_documents'
	]);
	Route::get('states/update', [
		'as' => 'states.update',
		'uses' => 'StatesController@update'
	]);
});
Route::group(array('before'=>'auth', 'before'=>'csrf'), function() {
	Route::post('payments_table/', [
		'as' => 'invoices.payments_table',
		'uses' => 'InvoicesController@payments_table'
	]);
	Route::post('earnings_table/', [
		'as' => 'invoices.earnings_table',
		'uses' => 'InvoicesController@earnings_table'
	]);
	Route::post('pay_table/', [
		'as' => 'invoices.pay_table',
		'uses' => 'InvoicesController@pay_table'
	]);
	Route::post('bill_table/', [
		'as' => 'invoices.bill_table',
		'uses' => 'InvoicesController@bill_table'
	]);
	Route::post('vendorrates/store', [
			'as' => 'vendorrates.store',
			'uses' => 'VendorRatesController@store'
	]);
	Route::post('locations/store', [
		'as' => 'locations.store',
		'uses' => 'LocationsController@store'
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
    Route::post('clientrates/update', [
        'as' => 'clientRates.update',
        'uses' => 'ClientRatesController@update'
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
Route::post('jobs/edit', [
		'as' => 'jobs.edit',
		'uses' => 'JobsController@edit'
	]);
Route::post('jobs/update', [
		'as' => 'jobs.update',
		'uses' => 'JobsController@update'
	]);
Route::post('jobs/save', [
		'as' => 'jobs.save',
		'uses' => 'JobsController@save'
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
Route::post('tasks/delete', [
		'as' => 'tasks.delete',
		'uses' => 'TasksController@delete'
	]);
Route::post('tasks/create_dec', [
	'as' => 'tasks.create_dec',
	'uses' => 'TasksController@create_dec'
	]);

Route::post('tasks/documents', [
	'as' => 'tasks.documents',
	'uses' => 'TasksController@documents'
	]);
Route::post('tasks/invoice', [
		'as' => 'tasks.invoice',
		'uses' => 'TasksController@invoice'
	]);
Route::post('tasks/assign', [
		'as' => 'tasks.assign',
		'uses' => 'TasksController@assign'
	]);
Route::post('tasks/vendor_print', [
		'as' => 'tasks.vendor_print',
		'uses' => 'TasksController@vendor_print'
	]);
Route::post('tasks/locate', [
		'as' => 'tasks.locate',
		'uses' => 'TasksController@locate'
	]);
Route::post('tasks/proof', [
	'as' => 'tasks.proof',
	'uses' => 'TasksController@proof'
	]);
Route::post('tasks/generate_proof', [
		'as' => 'tasks.generate_proof',
		'uses' => 'TasksController@generate_proof'
	]);
Route::post('tasks/upload_proof', [
			'as' => 'tasks.upload_proof',
			'uses' => 'TasksController@upload_proof'
	]);
Route::post('tasks/mailing', [
		'as' => 'tasks.mailing',
		'uses' => 'TasksController@mailing'
	]);
Route::post('tasks/generate_mailing', [
		'as' => 'tasks.generate_mailing',
		'uses' => 'TasksController@generate_mailing'
	]);
Route::post('tasks/upload_mailing', [
		'as' => 'tasks.upload_mailing',
		'uses' => 'TasksController@upload_mailing'
	]);

    Route::post('tasks/proofFiled', [
        'as' => 'tasks.proofFiled',
        'uses' => 'TasksController@proofFiled'
    ]);
Route::post('orders/edit/', [
		'as' => 'orders.edit',
		'uses' => 'OrdersController@edit'
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
Route::post('states/load', [
		'as' => 'states.load',
		'uses' => 'StatesController@load'
	]);
Route::post('states/save', [
		'as' => 'states.save',
		'uses' => 'StatesController@save'
	]);

Route::post('company/update', [
		'as' => 'company.save',
		'uses' => 'CompanyController@save'
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
Route::match(array('GET', 'POST'),'jobs/verify', [
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
Route::get('/date', [
	'as' => 'home.date',
	'uses' => 'HomeController@date'
]);
