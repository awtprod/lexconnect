@extends('layouts.default')
@section('head')
@section('content')
<h1>All Users</h1>

@if(Session::has('message'))
{{ Session::get('message') }}</br>
@endif

{{ link_to('/users/create', 'Add User') }}
@if ($users->count())

@foreach ($users as $user)
<li>{{ link_to("/users/{$user->id}", $user->fname.'&nbsp;'.$user->lname) }} &nbsp; {{ link_to("/users/{$user->id}/edit", 'Edit') }} &nbsp; {{ link_to("/users/delete/{$user->id}/", 'Delete') }}</li>
@endforeach
@else
<h2>No Users to display!</h2>
@endif
@stop
