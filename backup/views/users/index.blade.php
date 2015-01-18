@extends('layouts.default')

@section('content')
<h1>All Users</h1>
{{ link_to('/users/create', 'Add User') }}
@if ($users->count())

@foreach ($users as $user)
<li>{{ link_to("/users/{$user->id}", $user->name) }} &nbsp; {{ link_to("/users/{$user->id}/edit", 'Edit') }}</li> 
@endforeach
@else
<h2>No Users to display!</h2>
@endif
@stop
