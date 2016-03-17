@extends('layouts.default')
@section('head')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('#county').change(function(){
                $.get("{{ url('api/getRate')}}", { zipcode: $('#zipcode').val(), orderId: $('#orders_id').val(), type: $('#type').val(), client: $('#client').val(), priority: $('#priority').val(), county: $('#county').val(), state: $('#state').val()},
                        function(data) {
                            $('#rate').append( "Estimated Cost: " + data );
                            $('#submit').show();
                            $('#data').show();



                        });
            });
        });

    </script>
@stop
@section('content')


<h3>{{ link_to("/orders/{$orders_id}", "Order # {$orders_id}") }}</h3><p>

<h2> Verify Defendant</h2>

<!--If address was not verified-->


    {{ Form::open(['route' => 'jobs.store']) }}
<h3><font color="red">Warning! The Address You Entered Cannot Be Verified. Please Verify That You Correctly Entered The Address. Click "Verify Defendant" If You Wish To Continue With This Address.</font></h3>

@foreach($input["defendants"] as $defendant)
Defendant:{{ $defendant }}{{ Form::hidden('defendants[]', $defendant) }}<p>
@endforeach

{{ $input["street"] }}{{ Form::hidden('street', $input["street"]) }}&nbsp;
{{ $input["street2"] }}{{ Form::hidden('street2', $input["street2"]) }}&nbsp;
{{ $input["city"] }}{{ Form::hidden('city', $input["city"]) }},&nbsp;
{{ Form::select('county', $counties, null, ['id' => 'county']) }},&nbsp;
{{ $input["state"] }}<input id="state" type="hidden" name="state" value="{{$input["state"]}}">&nbsp;
{{ $input["zipcode"] }}<input id="zipcode" type="hidden" name="zipcode" value="{{$input["zipcode"]}}"><p>

    <div class="data" style="display:none">
    Estimated cost: <div class="rate"></div><input type="hidden" name="rate"><br>

</div>

    {{ Form::hidden('notes', Input::get('notes')) }}
    <input id="type" type="hidden" name="type" value="{{$input["type"]}}">
    <input id="priority" type="hidden" name="priority" value="{{$input["priority"]}}">
    <input id="client" type="hidden" name="client" value="{{$input["company"]}}">


@if(!empty($input["servee_id"]))
    <input id="orders_id" type="hidden" name="orders_id" value="{{$orders_id}}">
	{{ Form::hidden('servee_id', $input["servee_id"]) }}
	<div class="submit" style="display:none"><input type="submit" name="verify" value="Verify Defendant"></div><input type="submit" name="edit_add" value="Edit Defendant"></div>
	
@else
    <input id="orders_id" type="hidden" name="orders_id" value="{{$orders_id}}">
	<div class="submit" style="display:none"><input type="submit" name="verify" value="Verify Defendant"></div><input type="submit" name="edit_create" value="Edit Defendant"></div>
@endif


{{ Form::close() }}


@if(!empty($jobs) AND empty($input["servee_id"]))
<h2> Current Defendants: </h2><p>
@foreach ($jobs as $job)
<table>
  <tr>
    <th><b>Defendant: {{ $job->defendant }}</b></th>
  </tr>
<tr>
<td>{{ $job->street }}<br> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@elseif(!empty($jobs) AND !empty($input["servee_id"]))
<h2> Previous Attempted Addresses: </h2><p>
@foreach ($serveejobs as $job)
<table>
<td>{{ $job->street }}<br> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@endif

@stop
