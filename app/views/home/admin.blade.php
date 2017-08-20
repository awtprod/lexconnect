@extends('layouts.default')
@section('head')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
  <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
  <script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
  <script>
    $(document).ready(function() {
      function task_table() {

        $.get("{{ url('api/tasksTable')}}",
                function (data) {
                  $('#taskTable').html(data);
                });
      }
      task_table()
    })
  </script>
  <style>
    body .modal-dialog {
    /* new custom width */
    width: 70%;
    }
  </style>
@section('content')
 <input id="token" type="hidden" value="{{ csrf_token() }}">
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>
<div id="taskTable"></div>
<div id="dataModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><div id="task-title"></div> </h4>
      </div>
      <div class="modal-body" id="complete_task">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<a href="{{ URL::previous() }}">Go Back</a>

@stop
