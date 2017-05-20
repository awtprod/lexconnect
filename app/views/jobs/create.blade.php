<!DOCTYPE html>
<html>

<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
	<script src="https://d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
																																								   <script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
																																																																   <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script> <script>

	jQuery(document).ready(function($) {


        var ss = jQuery.LiveAddress({
            key: '5198528973891423290',
            verifySecondary: true,
            submitSelector: "#Add",
            autoVerify: false,
            submitVerify: true,
            waitForStreet: true

        });

        liveaddress.on("AddressAccepted", function (event, data, previousHandler) {
            var zipField = data.address.getDomFields()['Zipcode'];
            zipField.value = data.response.chosen ? data.response.chosen.components.zipcode : zipField.value;
            previousHandler(event, data);
        });

        $('.address').css("display", "none");
        $('.process_service').css("display", "none");

        $(".add_defendant_form").click(function (e) {

            e.preventDefault();

            $('.add_defendant_form').toggle();

            $('.address').toggle();

        });
    });

			$("#Add").click(function(e) {

		e.preventDefault();


		var namesData = "";

		var personal = "";

	if($("#defendant\\[1\\]").val()) {

		for (var j=1; j<= x; j++) {

		namesData += '<b>Defendant:&nbsp;' + $('#defendant\\['+j+'\\]').val() + '<input type="hidden" name="defendant['+i+'][servee][' + j + '][name]" value="' + $('#defendant\\['+j+'\\]').val() + '">&nbsp;';

	if ($('input:checkbox#personal\\['+j+'\\]').is(':checked')){

		namesData += 'Personal Service Only:&nbsp; Yes' + '<input type="hidden" name="defendant['+i+'][servee][' + j + '][personal]" value="yes"></b><br>';

	}
	else{

		namesData += 'Personal Service Only:&nbsp; No</b><br>';

	}

	}
	}
	else{

	alert("Please enter a defendant!");

		return;

	}


	if(!$("#Zipcode").val()){

	alert("Please enter a Zip Code!");

		return;

	}

	if(!$("#county").val()){

	alert("Please select a county!");

		return;

	}


	$('.add_defendant_form').show();

	$('.add').hide();

	$("#non-verified").hide();

	$("#occupied").hide();

	$("#results").append('<div><br><h3>Defendant #'+i+'</h3>Service Type:&nbsp;'+$("input[name=type]:checked").val() + " " + '<input type="hidden" name="defendant['+i+'][type]" value="'+$("input[name=type]:checked").attr('id')+'">&nbsp;'+
	'Priority:&nbsp;'+$('#priority').val() + " " + '<input type="hidden" name="defendant['+i+'][priority]" value="'+$('#priority').val()+'">&nbsp;<br>'+
	namesData+personal+
	$('#Street').val() + " " + '<input type="hidden" name="defendant['+i+'][street]" value="'+$('#Street').val()+'">&nbsp;'+
	$('#Street2').val() + '<input type="hidden" name="defendant['+i+'][street2]" value="'+$('#Street2').val()+'"><br>'+
	$('#City').val() + '<input type="hidden" name="defendant['+i+'][city]" value="'+$('#City').val()+'">,&nbsp;'+
	$('#county').val() + " " + '<input type="hidden" name="defendant['+i+'][county]" value="'+$('#county').val()+'">,&nbsp;'+
	$('#State').val() + " " + '<input type="hidden" name="defendant['+i+'][state]" value="'+$('#State').val()+'">&nbsp;'+
	$('#Zipcode').val() + " " + '<input type="hidden" name="defendant['+i+'][zipcode]" value="'+$('#Zipcode').val()+'"><br>'+
	$('#Notes').val() + " " + '<input type="hidden" name="defendant['+i+'][notes]" value="'+$('#Notes').val()+'"><br>'
	+'<button class="delete">Delete</button><br></div>');

	//reset variables

				$("#num_defendants").val(i);

	$('.input_fields_wrap').empty();

	$('.names input[type="text"]').val('');

	namesData = "";

	i++;

	x = 1;

	$('#defendant-info input[type="text"]').val('');

	$('#county').val('');

	$('.names input:checkbox').removeAttr('checked');

	$("#State").val('');

	$("#type").val('Process Service');

	$("#priority").val('Routine');

	});

	//Delete
			$("#results").on("click", ".delete", function() {
		$(this).closest('div').remove();


	});
	</script>
	<style>
table {
    width:50%;
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;
}
table#t01 tr:nth-child(even) {
    background-color: #eee;
}
table#t01 tr:nth-child(odd) {
   background-color:#fff;
}
table#t01 th	{
    background-color: black;
    color: white;
}
</style>
</head>

<body>



@if(!empty($input))

<h3>{{ link_to("/orders/{$input["orders_id"]}", "Order # {$input["orders_id"]}") }}</h3><p>

<h1>Add New Defendant</h1>

{{ Form::open(['route' => 'jobs.verify']) }}
		<div>
	@foreach($input['defendants'] as $defendant)
	{{ Form::label('defendants', 'Defendant: ') }}
	{{ Form::text('defendants', $defendant) }}
	{{ $errors->first('defendants') }}
@endforeach
		</div>

<h2>Defendants:</h2>

<button class="add_defendant_form"> Add Defendants</button>

<div class="address">


	<div id="service-type">
		{{ Form::label('type', 'Service Type: ') }}
		{{ Form::label('type', 'Process Service') }}
		<input type="radio" name="type" id="service" value="Process Service" checked>
		{{ Form::label('type', 'Property Posting') }}
		<input type="radio" name="type" id="post" value="Property Posting">
		{{ Form::label('priority', 'Priority: ') }}
		{{ Form::select('priority', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
	</div>

	<div class="names"><input type="text" id="defendant[1]"><input type="checkbox" name="personal" id="personal[1]">Personal Service Required</div>

	<div class="input_fields_wrap"></div>

	<button class="add_defendant_button">Add More Servees</button><br>


	<div id="defendant-info">

		Street:<input type="text" id="Street" name="Street"><br>
		Apt/Stuite/Unit:<input type="text" id="Street2"><br>
		City:<input type="text" id="City" name="City"><br>
		{{ Form::label('State', 'State: ') }}
		{{ Form::select('State', $states, null, ['id' => 'State']) }}<br>
		Zipcode:<input type="text" id="Zipcode" name="Zipcode"><br>
		Notes:<input type="text" id="Notes"><br>
	</div>

	<div id="occupied"></div>

	<div id="non-verified" style="display:none">
		<div class="row">
			<div class="large-9 columns">
				<label for="county">County:</label>
				<select id="county" name="county">
				</select>
			</div>
		</div></div>
	<button id="Add">Add</button><button class="add_defendant_form" style="display: none"> Cancel</button></p>
	</p>
</div>

<div id="results"></div></p>


</div></p>

<input type="hidden" name="num_defendants" id="num_defendants">

{{ Form::hidden('orders_id', $input["orders_id"]) }}

	<div>{{ Form::submit('Add Defendant') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
@else
<h3>{{ link_to("/orders/{$orders_id}", "Order # {$orders_id}") }}</h3><p>

<h1>Add New Defendant</h1>

{{ Form::open(['route' => 'jobs.verify']) }}
		<div>
	{{ Form::label('defendant', 'Defendant: ') }}
	{{ Form::text('defendant') }}
	{{ $errors->first('defendant') }}
	</div>
			<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street') }}
	{{ $errors->first('street') }}
	</div>
			<div>
	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('street2') }}
	{{ $errors->first('street2') }}
	</div>
			<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('city') }}
	{{ $errors->first('city') }}
	</div>
	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, null, ['id' => 'state']) }}
	{{ $errors->first('state') }}
	</div>
				<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('zipcode') }}
	{{ $errors->first('zipcode') }}
	</div>
<div>
    {{ Form::label('notes', 'Notes to Server: ') }}
    {{ Form::textarea('notes') }}
    {{ $errors->first('notes') }}<p>
</div>
<div>
	{{ Form::label('type', 'Service Type: ') }}
	{{ Form::radio('type', 'service', true) }}
	{{ Form::label('type', 'Process Service', true) }}
	{{ Form::radio('type', 'posting') }}
	{{ Form::label('type', 'Property Posting') }}
	{{ Form::label('service[priority]', 'Priority: ') }}
	{{ Form::select('service[priority]', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
</div>

{{ Form::hidden('orders_id', $orders_id) }}

	<div>{{ Form::submit('Add Defendant') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
@endif

@if(!empty($jobs))
<h2> Current Defendants: </h2><p>
@foreach ($jobs as $job)
<table>
  <tr>
    <th><b>Defendant: {{ $job->defendant }}</b></th>
  </tr>
<tr>
<td>{{ $job->street }}<p> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@endif
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
