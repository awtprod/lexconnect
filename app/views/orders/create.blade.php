<html>
<head>
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
    </script>
</head>
<body>
<h1>Create New Order</h1>


{{ Form::open(['route' => 'orders.store']) }}
	<div>
	{{ Form::label('plaintiff', 'Plaintiff: ') }}
	{{ Form::text('plaintiff') }}
	{{ $errors->first('plaintiff') }}
	</div>
		<div>
	{{ Form::label('defendant', 'Defendant: ') }}
	{{ Form::text('defendant') }}
	{{ $errors->first('defendant') }}
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
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, null, ['id' => 'state']) }}
	{{ $errors->first('state') }}
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

    {{ Form::label('Documents Served', 'Documents Served: ') }}<br>

    @foreach($documents as $document)

    {{ Form::checkbox($document[0], $document[1]) }}
    {{ Form::label($document[1],  $document[1]) }}<br>
    @endforeach
    {{ $errors->first('documentsServed') }}<p>
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
	{{ Form::label('type', 'Process Service', true) }}
	{{ Form::radio('service[type]', 'Process Service', true) }}
	{{ Form::label('type', 'Property Posting') }}
	{{ Form::radio('service[type]', 'Property Posting') }}
	{{ Form::label('priority', 'Priority: ') }}
	{{ Form::select('service[posting]', array(''=>'','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
</div>
<div>
	{{ Form::label('defendant', 'Defendant: ') }}
	{{ Form::text('service[defendant]') }}
	{{ $errors->first('defendant') }}
</div>
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

</body>
</html>
