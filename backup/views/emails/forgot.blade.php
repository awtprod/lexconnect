@extends('layouts.default')

@section('content')
 <h2>Reset Your Password</h2>


        <div>
            A request to reset your password has been received. If you do not request this,
            please contact customer support!
            Please follow the link below to reset your password
            {{ URL::to('users/password_reset/' . $password_reset) }}.<br/>

        </div>
@stop
