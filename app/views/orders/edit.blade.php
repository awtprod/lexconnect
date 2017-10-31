<html>
<head>
    <script>
        jQuery(document).ready(function($) {
					function getCourt(state, court){
                $.get("{{ url('api/getcourts')}}", { option: state },
                        function(data) {
                            var numbers = $('#court');
                            numbers.empty();
                            $.each(data, function(key, value) {

								if(key == court) {
									numbers.append($("<option></option>")
											.attr({"value": key, "selected":"selected"})
											.text(value))
								}
								else{
									numbers.append($("<option></option>")
											.attr({"value": key})
											.text(value));
								}
                            });
                        });
            }

			getCourt($('#state').val(),$('#current_court').val());

			$('#state').change(function(){
				getCourt($('#state').val(),$('#current_court').val())
			});

			$("#edit-form").submit(function(event){
				event.preventDefault();
				var form = $(this);

				$.ajax({
					method: 'POST', // Type of response and matches what we said in the route
					url: '/orders/update', // This is the url we gave in the route
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
</head>
<body>
<h1>Edit Order</h1>

{{ Form::open(array('route' => 'orders.update','id'=>'edit-form')) }}

<div>
	{{ Form::label('reference', 'Reference: ') }}
	{{ Form::text('reference', $data->reference) }}
</div>

<div>
	{{ Form::label('user', 'Requester: ') }}
	{{ Form::select('user', $users, $data->user, ['id' => 'FullName']) }}
</div>
@if(Auth::user()->user_role=='Admin')
	<div>
	{{ Form::label('plaintiff', 'Plaintiff: ') }}
	{{ Form::text('plaintiff', $data->plaintiff) }}
	</div>
		<div>
	{{ Form::label('defendant', 'Defendant: ') }}
	{{ Form::text('defendant', $data->defendant) }}
	</div>
		<div>
	{{ Form::label('case', 'Court Case: ') }}
	{{ Form::text('case', $data->courtcase) }}
	</div>

	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, $data->state, ['id' => 'state']) }}
	</div>
<div class="row">
    <div class="large-9 columns">
        <label for="court">Court:</label>
        <select id="court" name="court" selected>
        </select>
    </div>
</div>


    	{{ Form::label('company', 'Client: ') }}
	{{ Form::select('company', $clients, $data->company) }}
@endif

{{Form::hidden('orderId', $data->id)}}
<input id="current_court" type="hidden" value="{{ $data->court }}">


	<div>{{ Form::submit('Save Order') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}

</body>
</html>
