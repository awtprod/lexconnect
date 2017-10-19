<!DOCTYPE html>
<html>

<head>
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

{{ Form::open(array('route' => 'jobs.save','id'=>'edit-form')) }}
		<div>
			{{ Form::label('defendant', 'Defendant: ') }}
			{{ Form::text('defendant', $job->defendant) }}

	</div>
<div>
	{{ Form::label('vendor', 'Vendor: ') }}
	{{ Form::select('vendor', $vendors, $job->vendor) }}
</div>
			<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street', $job->street) }}
	</div>
	
@if(!empty($job->street2))
			<div>
	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('street2', $job->street2) }}
	</div>
@else
	<div>
		{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
		{{ Form::text('street2') }}
	</div>
@endif
			<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('city', $job->city) }}
	</div>
	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, $job->state) }}
	</div>
<div class="row">
	<div class="large-9 columns">
		<label for="county">County:</label>
		<select id="county" name="county">
		</select>
	</div>
</div>
				<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('zipcode', $job->zipcode) }}
	</div>
{{ Form::hidden('jobId', $job->id) }}
{{ Form::hidden('serveeId', $job->servee_id) }}
{{ Form::hidden('orderId', $job->order_id) }}
<input id="_token" name="_token" type="hidden" value="{{ csrf_token() }}">
<input id="current_county" type="hidden" value="{{ $job->county }}">


	<div>{{ Form::submit('Save') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}

</body>
<script>
	$(document).ready(function () {

		function getCounty(state,county){
			$.get("{{ url('api/getcounties')}}", { option: state },
					function(data) {
						var numbers = $('#county');
						numbers.empty();
						numbers .append($("<option></option>")
								.attr("value",'')
								.text('Select County'));
						$.each(data, function(key, value) {
							if(key == county) {
								numbers.append($("<option></option>")
										.attr({"value": key, "selected":"selected"})
										.text(value))
							}
							else{
								numbers.append($("<option></option>")
										.attr({"value": key})
										.text(value));
							}
							console.log(numbers)
						});
					});
		};

		getCounty($("#state").val(),$("#current_county").val());

	$("#state").change(function () {
		getCounty($("#state").val(),$("#current_county").val())
	});

	$("#edit-form").submit(function(event){
		event.preventDefault();
		var form = $(this);

		$.ajax({
			method: 'POST', // Type of response and matches what we said in the route
			url: '/jobs/save', // This is the url we gave in the route
			data: $(form).serialize(), // a JSON object to send back
			success: function(response){ // What to do if we succeed
				console.log(response);
				$('#editModal').modal('hide');
			},
			error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});

	});
	});

</script>
</html>
