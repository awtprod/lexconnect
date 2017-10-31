<?php
use Carbon\Carbon;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function date(){

		$tasks = Orders::where( DB::raw('YEAR(created_at)'), '=', date('Y')-1 )->get();;
		Carbon::setToStringFormat('F Y');
echo Carbon::now()->addMonths(-1);

	}
	public function redirect()
	{
		
		if(Auth::check()){
			
		return Redirect::route('home.index');
			
		}
		else{
			
		return View::make('hello');
		}
	}
	
	public function index()
	{
	
	//If User is Admin
	if(Auth::user()->user_role=='Admin'){

	Return View::make('home.admin');
		
	}
	
	//If User is Vendor
	
	elseif(Auth::user()->user_role=='Vendor'){


	Return View::make('home.vendor');
	}
	
	//If User is Client
	
	elseif(Auth::user()->user_role=='Client'){
		
	//Find Open Orders
	
	$openorders = DB::table('orders')
					->where('company', Auth::user()->company_id)
					->whereNULL('completed')->orderBy('created_at', 'asc')->get();
					
	Return View::make('home.client')->with(['openorders' => $openorders]);
		
	}
	
	//If Not Logged In, return to login screen
	
	else{
	
	return redirect::route('login');
	
	}
		
	}

}
