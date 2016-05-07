@extends('layouts.default')
@section('head')


	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
	<script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
    <script>

		jQuery(document).ready(function($) {
            $('#caseSt').change(function(){
                $.get("{{ url('api/getcourts')}}", { option: $('#caseSt').val() },
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


		function getCounty(state){
			$.get("{{ url('api/getcounties')}}", { option: state },
					function(data) {
						var numbers = $('#county');
						numbers.empty();
						numbers .append($("<option></option>")
								.attr("value",'')
								.text('Select County'));
						$.each(data, function(key, value) {
							numbers .append($("<option></option>")
									.attr("value",key)
									.text(value));
						});
					});
		};


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




		$(document).ready(function() {


			var ss = jQuery.LiveAddress({
				key: '5198528973891423290',
				verifySecondary: true,
				submitSelector: "#Add",
				debug: true

			});

			$('.address').css("display", "none");
			$('.process_service').css("display", "none");

			$(".add_defendant_form").click(function (e) {

				e.preventDefault();

				$('.add_defendant_form').toggle();

				$('.address').toggle();

			});


			$('.document_wrapper').change(function (e) {

				e.preventDefault();

				$(".supp_doc_type_select").each(function () {
					if ($(this).val() == "other") {

						$(this).next().show();
					}
					else {
						$(this).next().hide();
					}
				});
			});

			$('.service_documents').change(function(e){

				e.preventDefault();

				if($('.doc_type_select option:selected').val()=='other'){


					$('.doc_other').show();

				}
				else{

					$('doc_other').hide();
				}
			});

			$('.judicial').click(function () {

				if ($('input[name=judicial]:checked').val() == "judicial") {

					$("#judicial").slideDown("fast");

				} else {

					$("#judicial").slideUp("fast");

				}
			});

			//show/hide different service options
			$('.services').click(function(){



				if($('input[name=filing]:checked').val() == "filing"){


					$("#filing").slideDown("fast");

				}
				else{
					$("#filing").slideUp("fast");
				}

				if($('input[name=service]:checked').val() == "service"){

					$('.process_service').slideDown("fast");
				}
				else{

					$('.process_service').slideUp("fast");
				}

				if($('input[name=court_run]:checked').val() == "court_run"){

					$("#court_run").slideDown("fast");
				}
				else{

					$("#court_run").slideUp("fast");
				}
			});



			//Validate Data
			$("#create").validate({

				rules: {
					plaintiff: "required",
					'doc_other_text[]': "required",
					'documents[]': {
						required: false,
						accept: "application/pdf"
					},

				messages: {
					'documents[]':{
						accept: "Please upload document in PDF form."
					}
				}
				}

			});

			jQuery.extend(jQuery.validator.messages, {

				'documents[]':{
					accept: "Please upload document in PDF form."
				}

			});



			$("#defendant-info").change(function () {

				$("#occupied").empty();

				$(".add").hide();

				$("#non-verified").hide();

			});

			ss.on("AddressAccepted", function (event, data, previousHandler) {

				if (data.response.chosen) {

					var vacant = data.response.chosen.analysis.dpv_vacant;

					var occupancy = "";

					if (vacant == "N") {

						occupancy = "Yes";
					}
					else {

						occupancy = "No";
					}

					$(".add").show();

					$("#occupied").append('Occupied: ' + occupancy + '<input type="hidden" id="county" value="' + data.response.chosen.metadata.county_name + '">');

				}
				else {

					getCounty($("#State").val());

					$("#non-verified").show();

					$(".add").show();


				}

				previousHandler(event, data);

			});

		});
			$(document).ready(function() {

				var i = 1;

				//Additonal servees wrapper

				var wrapper         = $(".input_fields_wrap"); //Fields wrapper
				var add_button      = $(".add_defendant_button"); //Add button ID
				var x = 1;

				$(add_button).click(function(e){ //on add input button click
					e.preventDefault();
					$(wrapper).append('<div class="names"><input type="text" class="defendant"/><input type="checkbox" name="personal" class="personal" value="personal">Personal Service Required<a href="#" class="remove_field">Remove</a></div>'); //add input box
					x++;

				});

				$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
					e.preventDefault(); $(this).parent('.names').remove();
					x--;
				})

				//Court Run Wrapper
				var add_run_wrapper         = $(".add_court_run"); //Fields wrapper
				var add_run_button      = $(".add_court_run_button"); //Add button ID
				var y = 1;

				$(add_run_button).click(function(e){ //on add input button click
					e.preventDefault();
					$(add_run_wrapper).append('<div class="supp_court_run"><input type="text" class="run_docs" name="run_docs[]"><a href="#" class="remove_field">Remove</a></div>'); //add input box
					y++;

				});

				$(add_run_wrapper).on("click",".remove_field", function(e){ //user click on remove text
					e.preventDefault(); $(this).parent('.supp_court_run').remove();
					y--;
				})

				//Service Documents Wrapper

			var document_wrapper         = $(".document_wrapper"); //Fields wrapper
			var add_document_button      = $(".add_document_button"); //Add button ID

			$(add_document_button).click(function(e){ //on add input button click
				e.preventDefault();
				$(document_wrapper).append('<div class="additional_document">&nbsp;<input type="file" name="documents[]" class="documents">&nbsp;'+

						'<div class="doc_type"> <select class="supp_doc_type_select" name="doc_type[]">'+
						'<option value="">Select Document Type</option>'+

				@foreach($documents as $document)

                    '<option value=".{{ $document[0] }}.">'+
						'{{ $document[1] }}'+
                    '</option>'+
						@endforeach

                        '<option value="other">Other (Fill in below)</option>'+
						'</select>'+
						'<div class="supp_doc_other" style="display: none"><input type="text" name="doc_other_text[]" class="doc_other_text"></div>'+
						'</div>'+
						'<a href="#" class="remove_field">Remove</a></div>'); //add input box

			});

			$(document_wrapper).on("click",".remove_field", function(e){ //user click on remove text
				e.preventDefault(); $(this).parent('.additional_document').remove();
			})


			//Defendants wrapper
			$("#Add").click(function(e) {

				e.preventDefault();


				var namesData = "";

				var personal = "";


				var j = 0;

				if($('.defendant').val()) {

					$('.defendant').each(function () {

						if($(this).val()) {

							namesData += 'Defendant:&nbsp;' + $(this).val() + '<input type="hidden" name="defendant['+i+'][name][' + j + ']" value="' + $(this).val() + '"><br>';

							j++;
						}

					});
				}
				else{

					alert("Please enter a defendant!");

					return;

				}


				j=0;

				$('.personal').each(function(){


				if ($('input.personal').is(':checked')) {

					personal += '<input type="hidden" name="defendant['+i+'][personal]['+j+']" value="yes">';

				} else{

					personal += '<input type="hidden" name="defendant['+i+'][personal][' + j + ']" value="no">';

				}

					j++;

				});






				if(!$("#Zipcode").val()){

					alert("Please enter a Zip Code!");

					return;

				}

				if(!$("#county").val()){

					alert("Please select a county!");

					return;

				}

/*
				$('.add_defendant').each(function(){

					if($(this).val()){

						namesData +=$(this).val()+'<input type="hidden" name="defendants["'+i+'"]["'+j+'"]" value="'+$(this).val()+'"><br>';

						if ($('input.personal').is(':checked')) {

							namesData += 'Personal Service Required <input type="hidden" name="personal["'+i+'"]["'+j+'"]" value="yes"><br>'
						}

						j++;

					}
				});

*/

				$('.add_defendant_form').show();

				$('.add').hide();

				$("#non-verified").hide();

				$("#occupied").hide();


				$("#results").append('<div><br><h3>Defendant #'+i+'</h3>Service Type:&nbsp;'+$("input[name=type]:checked").val() + " " + '<input type="hidden" name="defendant['+i+'][type]" value="'+$("input[name=type]:checked").val()+'">&nbsp;'+
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

				$("#num_defendants").val(i);

				$('.input_fields_wrap').empty();

				$('.names input[type="text"]').val('');

				namesData = "";

				i++;

				$('#defendant-info input[type="text"]').val('');

				$("#State").val('');

				$("#type").val('Process Service');

				$("#priority").val('Routine');

			});

			//Delete
			$("#results").on("click", ".delete", function() {
				$(this).closest('div').remove();

			});
		});

	</script>
<style>


</style>

@stop
@section('content')
<h1>Create New Order</h1>

@if (Auth::user()->user_role=='Admin')
	{{ Form::label('company', 'Client: ') }}
	{{ Form::select('company', $company) }}
@else
	{{ Form::hidden('company', $company) }}
@endif


@if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

{{ Form::open(array('route' => 'orders.store','files'=>true, 'id'=> 'create')) }}

	<input type="radio" name="judicial" class="judicial" value="judicial" checked>Judicial
	<input type="radio" name="judicial" class="judicial" value="non_judicial">Non Judicial<p>


	{{ Form::label('reference', 'Reference: ') }}
	{{ Form::text('reference') }}<br>

	<div id="judicial">
	{{ Form::label('plaintiff', 'Plaintiff: ') }}
	{{ Form::text('plaintiff') }}<br>

	{{ Form::label('case', 'Court Case: ') }}
	{{ Form::text('case') }}<br>


<div class="row">
    <div class="large-9 columns">
        <label for="court">Court:</label>
		{{ Form::select('caseSt', $states, null, ['id' => 'caseSt']) }}
		<select id="court" name="court">
        </select>
	</div>
</div>
		</div><p>

    	<div id="service_options">
	<h2>Services:</h2>
	<input type="checkbox" name="service" class="services" value="service">Process Service &nbsp;
	<input type="checkbox" name="filing" class="services" value="filing">Filing/Recording &nbsp;
	<input type="checkbox" name="court_run" class="services" value="court_run">Court Run

</div>

	<div id="filing" style="display:none;">

	<h2>Filing/Recording:</h2></p>

	{{ Form::label('filing', 'Filing: ') }}
	{{ Form::select('filing', array(''=>'Select Priority Level','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<br>

	{{ Form::label('recording', 'Recording: ') }}
	{{ Form::select('recording', array(''=>'Select Priority Level','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<br>
	</div>


	<div id="court_run" style="display:none;">
		<h2>Document Retrieval</h2></p>

		<input type="text" class="run_docs" name="run_docs">

	<div class="add_court_run"></div>

	<button class="add_court_run_button">Add More Documents</button><br>
	</div>

	<div class="process_service">

	<div class="service_documents">


<h2>Service Documents:</h2>
		<input type="file" name="documents[]" class="documents">&nbsp;

		<article> <select class="doc_type_select">
			<option value="">Select Document Type</option>

			@foreach($documents as $document)

				<option value=".{{ $document[0] }}.">
					{{ $document[1] }}
				</option>
			@endforeach

			<option value="other">Other (Fill in below)</option>
		</select>
		<div class="doc_other" style="display: none"><input type="text" name="doc_other_text[]" class="doc_other_text"><br></div>
		</article>
		<br>

		<div class="document_wrapper"></div>

		<button class="add_document_button">Add More Documents</button>

		</div>

    @foreach($documents as $document)
   {{  '<input type="hidden" name="document_types[]" value="'. $document[0]. '">' }}
    @endforeach
<p>

		<h2>Defendants:</h2>

<button class="add_defendant_form"> Add Defendants</button>

<div class="address">


	<div id="service-type">
		{{ Form::label('type', 'Service Type: ') }}
		{{ Form::label('type', 'Process Service') }}
		<input type="radio" name="type" value="Process Service" checked>
		{{ Form::label('type', 'Property Posting') }}
		<input type="radio" name="type" value="Property Posting">
		{{ Form::label('priority', 'Priority: ') }}
		{{ Form::select('priority', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
	</div>

	<div class="names"><input type="text" class="defendant" name="defendant"><input type="checkbox" name="personal" class="personal" value="personal">Personal Service Required</div>

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

		<div><button id="Submit" class="Submit">Submit</button>{{ Form::reset('Reset') }}</div>
{{ Form::close() }}



<a href="{{ URL::previous() }}">Go Back</a>

@stop
