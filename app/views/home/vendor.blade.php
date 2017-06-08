@extends('layouts.default')
@section('head')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
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
@section('content')
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>

<div id="taskTable"></div>
  <div id="dataModal" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Complete Task</h4>
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

<script>
  $(document).ready(function() {

    $('.view_data').click(function () {
      var task_id = $(this).attr("id");
      $.get("{{ url('tasks/test')}}", { id: task_id },
              function(data) {
          console.log(data);
          $('#complete_task').html(data);
          $('#dataModal').modal("show");
        });
    });
    });
</script>
@stop
