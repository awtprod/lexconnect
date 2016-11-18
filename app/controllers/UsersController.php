<?php

class UsersController extends \BaseController {
	protected $user;
	

	public function index()
	{
		if (Auth::check()){

		if(Auth::user()->user_role == 'Admin') {

			$users = User::all();
		}
		elseif(Auth::user()->role == 'Supervisor'){

			$users = User::wherecompanyId(Auth::user()->company_id)->orderBy('id', 'asc')->get();
		}
		else{
			return "Not Authorized";
		}
		
		return View::make('users.index', ['users' => $users]);
		}
		return Redirect::route('login');

	}
	
	public function show($id)
	{
		if (Auth::check()){
		$user = $this->user->whereId($id)->first();
	
		return View::make('users.show', ['user' => $user]);	
		}
		return Redirect::route('login');
	}
	
	public function create()
	{
		if (Auth::check()){
		$company = DB::table('company')->orderBy('name', 'asc')->lists('name', 'name');
		return View::make('users.create', array('company' => $company));
		}
		return Redirect::route('login');
	}
	
	public function store()
	{
		$input = Input::all();
		
		if ( ! $this->User->fill($input)->isValid())
			{
				return Redirect::back()->withInput()->withErrors($this->user->errors);	
			}
			
			
		        $activation_code = str_random(30);
		        $company_id = DB::table('company')->where('name', Input::get('company'))->pluck('id');

        User::create([
            'email' => Input::get('email'),
            'fname' => Input::get('fname'),
			'lname' => Input::get('lname'),
			'company' => Input::get('company'),
            'role' => Input::get('role'),
            'user_role' => Input::get('user_role'),
            'company_id' => $company_id,
            'activation_code' => $activation_code
        ]);

        Mail::send('emails.verify', ['activation_code' => $activation_code], function($message) {
            $message->to(Input::get('email'))
                ->subject('Verify your email address');
        });

        //Flash::message('New User Created!');
		
		return Redirect::route('users.index');
	}

	public function edit($id)
	{
		if (Auth::check()){

			$user = User::whereId($id)->first();

			if(Auth::user()->user_role == 'Admin' OR (Auth::user()->role == 'Supervisor' AND Auth::user()->company_id == $user->company_id)) {

				$company = DB::table('company')->orderBy('name', 'asc')->lists('name', 'name');
				$user = User::whereId($id)->first();
				return View::make('users.edit', ['company' => $company], ['user' => $user]);

			}
		}
		return Redirect::route('login');
	}
	public function store_edit()
	{
		$input = Input::all();
		$user = User::find(Input::get('id'));

		if ($user->email == Input::get('email'))
		{
			if ( ! $this->User->fill($input)->isValid())
			{
				return Redirect::back()->withInput()->withErrors($this->User->errors);
			}
			$user->fname = Input::get('fname');
			$user->lname = Input::get('lname');
			$user->company = Input::get('company');
			$user->role = Input::get('role');
			$user->user_role = Input::get('user_role');
			$user->company_id = Input::get('company_id');
			$user->save();
			return Redirect::route('users.index');
		}
		elseif ( ! $this->user->fill($input)->isValidAll())
		{
			return Redirect::back()->withInput()->withErrors($this->user->errors);
		}
		$user->email = Input::get('email');
		$user->fname = Input::get('fname');
		$user->lname = Input::get('lname');
		$user->company = Input::get('company');
		$user->role = Input::get('role');
		$user->user_role = Input::get('user_role');
		$user->company_id = Input::get('company_id');
		$user->save();
		return Redirect::route('users.show', $input{id});
	}

	public function delete($id){

		if (Auth::check()){

			$user = User::whereId($id)->first();

			if(Auth::user()->user_role == 'Admin' OR (Auth::user()->role == 'Supervisor' AND Auth::user()->company_id == $user->company_id)) {

				$user->delete();

				return View::make('user.index');

			}
		}
		return Redirect::route('login');
	}

	public function resend_activation()
	{
		return View::make('users.resend_activation');
	}
	
	public function push_resend_activation()
	{
		$email = Input::only('email');
		$user = User::whereEmail($email)->first();
		$activation_code = str_random(30);
		$user->activation_code = $activation_code;
		$user->save();
		Mail::send('emails.verify', ['activation_code' => $activation_code], function($message) {
	        $message->to(Input::get('email'))
                ->subject('Verify your email address');
        });
		return Redirect::to('/');
	}
	
	public function activation($activation_code)
    {
        if( ! $activation_code)
        {
            throw new InvalidActivationCodeException;
        }
        return View::make('users.new_password', ['activation_code' => $activation_code]);


        //Flash::message('You have successfully verified your account. Please create a password');

       // return Redirect::route('new_password/', ['user' => $user]);
        //return View::make('users.new_password');
    }
    	public function new_password($activation_code)
    	{
    	if( ! $activation_code)
        {
            return Redirect::route('users.index');
        }
    	$input = Input::all();	
    	if ( ! $this->user->fill($input)->isValidPassword())
	{
		return Redirect::back()->withInput()->withErrors($this->user->errors);	
	}

	$user = User::whereActivationCode($activation_code)->first();
        if ( ! $user)
        {
            return Redirect::route('users.index');
        }

        $user->activation = 1;
        $user->activation_code = null;
        $user->password = Hash::make(Input::get('password'));
        $user->save();
          	return Redirect::route('users.index');
    	}

	
	public function forgot_password()
	{
		Return View::make('users.forgot_password');
	}
	
	public function push_forgot_password()
	{
		$input = Input::all();

		$email = Input::only('email');
		$user = User::whereEmail($email)->first();

		if (empty($user)){
		Return Redirect::back()->withInput()->withErrors('Email Not Found!');
		}
		$password_reset = str_random(30);
		$user->password_reset = $password_reset;
		$user->save();
		Mail::send('emails.forgot', ['password_reset' => $password_reset], function($message) {
	        $message->to(Input::get('email'))
                ->subject('Reset Your Password');
        });
		return Redirect::to('/');	
	}
	
	public function password_reset($password_reset)
	{
	$user = User::wherePasswordReset($password_reset)->first();
	if (empty($user)){
		Return View::make('forgot_password')->withErrors('Invalid Reset Code!');
	}
	Return View::make('users.password_reset', ['password_reset' => $password_reset]);
	}
	public function post_reset_password()
	{
	   $input = Input::all();

	$password_reset = Input::get('password_reset');
	$user = User::wherePasswordReset($password_reset)->first();
        if ( ! $user)
        {
            return Redirect::route('users.index');
        }	
        $user->password_reset = null;
        $user->password = Hash::make(Input::get('password'));
        $user->save();
        
        Return Redirect::to('/');
	}
	public function test()
	{
		$companies = DB::table('company')->get();
		//$companies = DB::select('select * from company where id = ?', array(1));
		
		return View::make('users.company', ['companies' => $companies]);
		//return View::make('users.company', ['company' => $company]);
	}

}
