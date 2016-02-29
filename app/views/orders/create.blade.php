@extends('layouts.default')
@section('head')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('#state').change(function(){
                $.get("{{ url('api/getcourts')}}", { option: $('#state').val() },
                        function(data) {
                            var numbers = $('#court');
                            numbers.empty();
                            $.each(data, function(key, value) {
                                numbers .append($("<option></option>")
                                        .attr("value",key)
                                        .text(value));
                            });
                        });
            });
        });

		$(document).ready(function() {
			var max_fields      = 10; //maximum input boxes allowed
			var wrapper         = $(".input_fields_wrap"); //Fields wrapper
			var add_button      = $(".add_field_button"); //Add button ID

			var x = 1; //initlal text box count
			$(add_button).click(function(e){ //on add input button click
				e.preventDefault();
				if(x < max_fields){ //max input box allowed
					x++; //text box increment
					$(wrapper).append('<div>{{ Form::label('defendant', 'Defendant: ') }}<input type="text" name="service[defendants][]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
				}
			});

			$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
				e.preventDefault(); $(this).parent('div').remove(); x--;
			})
		});
    </script>
@stop
@section('content')
<h1>Create New Order</h1>

@if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

{{ Form::open(array('route' => 'orders.store','files'=>true)) }}
	<div>
	{{ Form::label('plaintiff', 'Plaintiff: ') }}
	{{ Form::text('plaintiff') }}
	{{ $errors->first('plaintiff') }}
	</div>
		<div>
	{{ Form::label('case', 'Court Case: ') }}
	{{ Form::text('case') }}
	{{ $errors->first('case') }}
	</div>
			<div>
	{{ Form::label('reference', 'Reference: ') }}
	{{ Form::text('reference') }}
	{{ $errors->first('reference') }}
	</div>

	<div>
	{{ Form::label('caseState', 'State: ') }}
	{{ Form::select('caseState', $states, null, ['id' => 'state']) }}
	{{ $errors->first('caseState') }}
	</div>
<div class="row">
    <div class="large-9 columns">
        <label for="court">Court:</label>
        <select id="court" name="court">
        </select>
		{{ $errors->first('court') }}
	</div>
</div>
    	<div>
	{{ Form::label('services', 'Services: ') }}
	{{ Form::label('filing', 'Filing: ') }}
	{{ Form::select('filing', array(''=>'','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}
	{{ Form::label('recording', 'Recording: ') }}
	{{ Form::select('recording', array(''=>'','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}
	{{ $errors->first('services') }}<p>
	</div>
<div>

	{{ $errors->first('service_documents') }}<p>
		{{ Form::label('Upload Service Documents: ') }}<input type="file" name="service_documents" id="">
		<br/>

    {{ Form::label('Documents Served', 'Documents Served: ') }}<br>

    @foreach($documents as $document)

    {{ Form::checkbox('documentServed['.$document[0].']', 'yes') }}
    {{ Form::label('documentServed['.$document[1].']',  $document[1]) }}<br>
    @endforeach
    {{ $errors->first('documentServed') }}<p>
</div>
@if (Auth::user()->user_role=='Admin')
    	{{ Form::label('company', 'Client: ') }}
	{{ Form::select('company', $company) }}
	{{ $errors->first('company') }}<p>
@else
	{{ Form::hidden('company', $company) }}
@endif

    @foreach($documents as $document)
   {{  '<input type="hidden" name="documents[]" value="'. $document[0]. '">' }}
    @endforeach

<h1>Add New Defendant</h1>
<div>
	{{ Form::label('type', 'Service Type: ') }}
	{{ Form::radio('type', 'service', true) }}
	{{ Form::label('type', 'Process Service', true) }}
	{{ Form::radio('type', 'posting') }}
	{{ Form::label('type', 'Property Posting') }}
	{{ Form::label('service[priority]', 'Priority: ') }}
	{{ Form::select('service[priority]', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
</div>
<div class="input_fields_wrap">
	<div>{{ Form::label('defendants', 'Defendant: ') }}<input type="text" name="service[defendants][]"></div>
	{{ $errors->first('defendants') }}
</div>
<button class="add_field_button">Add More Defendants</button>
<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('service[street]') }}
	{{ $errors->first('street') }}
</div>
<div>
	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('service[street2]') }}
	{{ $errors->first('street2') }}
</div>
<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('service[city]') }}
	{{ $errors->first('city') }}
</div>
<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('service[state]', $states, null, ['id' => 'state']) }}
	{{ $errors->first('state') }}
</div>
<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('service[zipcode]') }}
	{{ $errors->first('zipcode') }}
</div>
<div>
	{{ Form::label('notes', 'Notes to Server: ') }}
	{{ Form::textarea('service[notes]') }}
	{{ $errors->first('notes') }}<p>
</div>

	<div>{{ Form::submit('Create Order') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>

@stop
