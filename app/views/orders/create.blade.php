@extends('layouts.default')
@section('head')


	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
	<script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
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

		$('.service_documents').change(function(e){

			e.preventDefault();


			if($('.doc_type_select option:selected').val()=="other"){


				$(this).find('.doc_other').show();

			}
			else{

				$(this).find('.doc_other').hide();
			}

			return false;

		});

			$('.document_wrapper').change(function(e){

				e.preventDefault();

				$(".supp_doc_type_select").each(function()
				{
					if($(this).val()=="other"){

						$(this).next().show();
				}
				else{
						$(this).next().hide();
				}
				});
			});

		$(".address").css("display", "none");

		$('.judicial').click(function(){

			if ($('input[name=judicial]:checked').val()=="judicial"){

				$("#judicial").slideDown("fast");

			}	else {

				$("#judicial").slideUp("fast");

			}

		});


			//Validate Data
			$("#create").validate({

				rules:	{
					plaintiff: "required",
					defendant: "required",
					Street: "required",
					City: "required",
					Zipcode: "required"
				}

			});

			$('.add_defendant_form').click(function(e){

				e.preventDefault();

				$('.add_defendant_form').hide();

				$('.address').show();

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

			var i = 0;

			var ss = jQuery.LiveAddress({
				key: '5198528973891423290',
				waitForStreet: true,
				verifySecondary: true

			});

			$("#defendant-info").change(function(){

				$("#occupied").empty();

				$(".add").hide();

				$("#non-verified").hide();

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

					$(".add").show();

					$("#occupied").append('Occupied: '+occupancy+'<input type="hidden" id="county" value="'+data.response.chosen.metadata.county_name+'">');

				}
				else{

					getCounty($("#State").val());

					$("#non-verified").show();

					$(".add").show();


				}

				previousHandler(event, data);

				});

			$(document).ready(function() {

				var wrapper         = $(".input_fields_wrap"); //Fields wrapper
				var add_button      = $(".add_defendant_button"); //Add button ID
				var x = 1;

				$(add_button).click(function(e){ //on add input button click
					e.preventDefault();
					$(wrapper).append('<div class="names"><input type="text" class="add_defendant"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
					x++;

				});

				$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
					e.preventDefault(); $(this).parent('.names').remove();
					x--;
				})

				var add_run_wrapper         = $(".add_court_run"); //Fields wrapper
				var add_run_button      = $(".add_court_run_button"); //Add button ID
				var y = 1;

				$(add_run_button).click(function(e){ //on add input button click
					e.preventDefault();
					$(add_run_wrapper).append('<div class="supp_court_run"><input type="text" class="run_docs" name="run_docs"><a href="#" class="remove_field">Remove</a></div>'); //add input box
					y++;

				});

				$(add_run_wrapper).on("click",".remove_field", function(e){ //user click on remove text
					e.preventDefault(); $(this).parent('.supp_court_run').remove();
					y--;
				})

				var doc_serve_wrapper         = $("#doc_served_wrapper"); //Fields wrapper
				var add_doc_serve_button      = $("#add_doc_served_button"); //Add button ID

				$(add_doc_serve_button).click(function(e){ //on add input button click
					e.preventDefault();

					if(!$("#doc_served_select_options").val()) {

						alert("Please enter document type!");

						return;
					}

					if($("#doc_served_select_options option:selected").val() == "other"){

						if(!$("#doc_other_text").val()) {

							alert("Please enter document type!");

							return;
						}
					}

					if($("#doc_served_select_options option:selected").val() == "other"){

						$(doc_serve_wrapper).append('<div class="doc_served_list"><input type="hidden" class="docs_served" name="docs_served[]" value="' + $("#doc_other_text").val() + '">' + $("#doc_other_text").val() + '<a href="#" class="remove_field">Remove</a></div>'); //add input box

					}
					else {

						$(doc_serve_wrapper).append('<div class="doc_served_list"><input type="hidden" class="docs_served" name="docs_served[]" value="' + $("#doc_served_select_options option:selected").text() + '">' + $("#doc_served_select_options option:selected").text() + '<a href="#" class="remove_field">Remove</a></div>'); //add input box
					}

					$("#doc_served_select_options").val('');

					$("#doc_other").hide();

					$("#doc_other_text").val('');


				});

				$(doc_serve_wrapper).on("click",".remove_field", function(e){ //user click on remove text
					e.preventDefault(); $(this).parent('.doc_served_list').remove();
				})
			});

			var document_wrapper         = $(".document_wrapper"); //Fields wrapper
			var add_document_button      = $(".add_document_button"); //Add button ID

			$(add_document_button).click(function(e){ //on add input button click
				e.preventDefault();
				$(document_wrapper).append('<div class="additional_document">&nbsp;<input type="file" name="documents[]" class="documents">&nbsp;'+

						'<div class="doc_type"> <select class="supp_doc_type_select">'+
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

			//Add to array
			$('.Add').click(function(e) {

				e.preventDefault();


				var namesData = "";

				var j = 0;

				if($('.defendant').val()){

					namesData +=$('.defendant').val()+'<input type="hidden" name="defendants["'+i+'"]["'+j+'"]" value="'+$('.defendant').val()+'"><br>';


					j++;

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


				$('.add_defendant').each(function(){

					if($(this).val()){

						namesData +=$(this).val()+'<input type="hidden" name="defendants["'+i+'"]["'+j+'"]" value="'+$(this).val()+'"><br>';

						j++;

					}
				});

				$('.add_defendant_form').show();

				$('.add').hide();

				$("#non-verified").hide();

				$("#occupied").hide();


				$("#results").append('<div><br>'+namesData+
						$('#Street').val() + " " + '<input type="hidden" name="street["'+i+'"]" value="'+$('#Street').val()+'">&nbsp;'+
						$('#Street2').val() + '<input type="hidden" name="street2["'+i+'"]" value="'+$('#Street2').val()+'"><br>'+
						$('#City').val() + '<input type="hidden" name="city["'+i+'"]" value="'+$('#City').val()+'">,&nbsp;'+
						$('#county').val() + " " + '<input type="hidden" name="county["'+i+'"]" value="'+$('#county').val()+'">,&nbsp;'+
						$('#State').val() + " " + '<input type="hidden" name="state["'+i+'"]" value="'+$('#State').val()+'">&nbsp;'+
						$('#Zipcode').val() + " " + '<input type="hidden" name="zipcode["'+i+'"]" value="'+$('#Zipcode').val()+'"><br>'+
						$('#Notes').val() + " " + '<input type="hidden" name="notes["'+i+'"]" value="'+$('#Notes').val()+'"><br>'
						+'<button class="delete">Delete</button><br></div>');

				$('.input_fields_wrap').empty();

				$('.names input[type="text"]').val('');


				i++;

				$('#defendant-info input[type="text"]').val('');

			});

			//Delete
			$("#results").on("click", ".delete", function() {
				$(this).closest('div').remove();

			});
		});

	</script>
<style>
	.smarty-ui{
		position: relative; !important;
		top: 60px; !important;
	}

</style>

@stop
@section('content')
<h1>Create New Order</h1>

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

    	<div id="services_options">
	{{ Form::label('services', 'Services: ') }}<br>
	<input type="checkbox" name="services" class="services" value="filing">Filing &nbsp;
	<input type="checkbox" name="services" class="services" value="recording">Recording &nbsp;
	<input type="checkbox" name="services" class="services" value="court-run">Court Run

</div>

	<div id="filing">
	{{ Form::label('filing', 'Filing: ') }}
	{{ Form::select('filing', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<br>
		</div>

	<div id="recording">
	{{ Form::label('recording', 'Recording: ') }}
	{{ Form::select('recording', array(''=>'','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<br>
	</div>

	<div id="court_run">
		<input type="text" class="run_docs" name="run_docs"></div>

	<div class="add_court_run"></div>

	<button class="add_court_run_button">Add More Documents</button><br>

	<div class="service_documents">


		{{ Form::label('Upload Service Documents: ') }}<br>
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


@if (Auth::user()->user_role=='Admin')
    	{{ Form::label('company', 'Client: ') }}
	{{ Form::select('company', $company) }}
@else
	{{ Form::hidden('company', $company) }}
@endif

    @foreach($documents as $document)
   {{  '<input type="hidden" name="document_types[]" value="'. $document[0]. '">' }}
    @endforeach
<p>


<button class="add_defendant_form"> Add Defendant</button>

<div class="address">

<h1>Add New Defendant</h1>

	<div class="names"><input type="text" class="defendant" name="defendant"></div>

<div class="input_fields_wrap"></div>

<button class="add_defendant_button">Add More Defendants</button><br>

	<div id="service-type">
		{{ Form::label('type', 'Service Type: ') }}
		{{ Form::radio('type', 'service', true) }}
		{{ Form::label('type', 'Process Service', true) }}
		{{ Form::radio('type', 'posting') }}
		{{ Form::label('type', 'Property Posting') }}
		{{ Form::label('priority', 'Priority: ') }}
		{{ Form::select('priority', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
	</div>

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

</div>
<button class="Add" style="display:none">Add</button></p>
		<div id="results"></div></p>

		<div><button id="Submit" class="Submit">Submit</button>{{ Form::reset('Reset') }}</div>
{{ Form::close() }}



<a href="{{ URL::previous() }}">Go Back</a>

@stop
