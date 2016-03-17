@extends('layouts.default')
@section('head')


	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
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

	<script>
		jQuery(document).ready(function($) {
			function getCounty(state){
				$.get("{{ url('api/getcounties')}}", { option: state },
						function(data) {
							var numbers = $('#county');
							numbers.empty();
							$.each(data, function(key, value) {
								numbers .append($("<option></option>")
										.attr("value",key)
										.text(value));
							});
						});
			};
		});
	</script>

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



	<script>
		$(document).ready(function() {

			var i = 0;

			var ss = jQuery.LiveAddress({
				key: '5198528973891423290',
				waitForStreet: true,
				verifySecondary: true,
				address: [{
					state: '#State'
				}]

			});

			$('form').change(function(){

				$("#occupied").empty();

			});

			ss.on("AddressAccepted", function(event, data, previousHandler) {

				if (data.response.chosen) {

					var vacant = data.response.chosen.analysis.dpv_vacant;

					var occupancy = "";

					if(vacant == "N"){

						occupancy = "Yes";
					}
					else{

						occupancy = "No";
					}

					$("#occupied").append('Occupied: '+occupancy);

				}

				previousHandler(event, data);

				});

			$(document).ready(function() {

				var wrapper         = $(".input_fields_wrap"); //Fields wrapper
				var add_button      = $(".add_defendant_button"); //Add button ID
				var x = 1;

				$(add_button).click(function(e){ //on add input button click
					e.preventDefault();
					$(wrapper).append('<div class="names"><input type="text" class="defendant"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
					x++;

				});

				$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
					e.preventDefault(); $(this).parent('.names').remove();
					x--;
				})
			});

			//Add to array
			$('.Add').click(function() {

				$(this).attr('disabled','disabled');

				var namesData = "";

				var j = 0;

				$('.defendant').each(function(){

					if($(this).val()){

						namesData +=$(this).val()+'<input type="hidden" name="defendants["'+i+'"]["'+j+'"]" value="'+$(this).val()+'"><br>';

						j++;
					}
				});
				$("#results").append('<div><br>'+namesData+
						$('#Street').val() + " " + '<input type="hidden" name="street["'+i+'"]" value="'+$('#Street').val()+'"><br>'+
						$('#City').val() + '<input type="hidden" name="city["'+i+'"]" value="'+$('#City').val()+'">,'+
						$('#State').val() + " " + '<input type="hidden" name="state["'+i+'"]" value="'+$('#State').val()+'">&nbsp;'+
						$('#Zipcode').val() + " " + '<input type="hidden" name="zipcode["'+i+'"]" value="'+$('#Zipcode').val()+'"><br>'+
						$('#Notes').val() + " " + '<input type="hidden" name="notes["'+i+'"]" value="'+$('#Notes').val()+'"><br>'
						+'<button class="delete">Delete</button><br></div>');

				$('.input_fields_wrap').empty();

				$('.input_fields_wrap').append('<div class="names"><input type="text" class="name"/></div>'); //add input box

				i++;
				$(this).closest('form').find("input[type=text], textarea").val("");


			});

			//Delete
			$("#results").on("click", ".delete", function() {
				$(this).closest('div').remove();

			});
		});

	</script>
<style>
	.smarty-popup{
		position: relative; !important;
		top: 6px; !important;
	}
</style>

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
	{{ $errors->first('blah') }}
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





<div>
{{ Form::label('type', 'Service Type: ') }}
{{ Form::radio('type', 'service', true) }}
{{ Form::label('type', 'Process Service', true) }}
{{ Form::radio('type', 'posting') }}
{{ Form::label('type', 'Property Posting') }}
{{ Form::label('priority', 'Priority: ') }}
{{ Form::select('priority', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
	</div>


<h1>Add New Defendant</h1>
<div class="address">


<div class="input_fields_wrap">
	<div class="names"><input type="text" class="defendant"></div></div>

<button class="add_defendant_button">Add More Defendants</button><br>

	Street:<input type="text" id="Street"><br>
	Apt/Stuite/Unit:<input type="text" id="Street2"><br>
	City:<input type="text" id="City"><br>
	{{ Form::label('State', 'State: ') }}
	{{ Form::select('State', $states, null, ['id' => 'state']) }}<br>
	Zipcode:<input type="text" id="Zipcode"><br>
	Notes:<input type="text" id="Notes"><br>

	<div id="occupied"></div>

</div>
<button class="Add">Add</button></p>
		<div id="results"></div></p>

		<div><button id="Submit" class="Submit">Submit</button>{{ Form::reset('Reset') }}</div>
{{ Form::close() }}



<a href="{{ URL::previous() }}">Go Back</a>

@stop
