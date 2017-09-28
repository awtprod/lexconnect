@extends('layouts.default')
@section('head')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
    <script src="https://d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>

    <script>

        jQuery(document).ready(function() {
            //Additonal servees wrapper

            var wrapper         = $(".input_fields_wrap"); //Fields wrapper
            var add_button      = $(".add_defendant_button"); //Add button ID
            var x = 1;

            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                x++;
                $(wrapper).append('<div class="names"><input type="text" id="defendants['+x+'][name]" name="defendants['+x+'][name]" required/><input type="checkbox" name="defendants['+x+'][personal]" id="defendants['+x+'][personal]">Personal Service Required<a href="#" class="remove_field">Remove</a></div>'); //add input box

            });
            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('.names').remove();
                x--;
            })

            $("#checkAll").change(function () {
                $("input:checkbox").prop('checked', $(this).prop("checked"));
            });

            //Validate Data
            $("#defendant_form").validate({

                rules: {
                    county: "required"
                },
            });

        });
    </script>
    <style>
        .smarty-ui {
            z-index: 99999;}
        .smarty-tag {
            opacity: 0;
        }
    </style>
@stop
@section('content')
    <div id="attemptModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">View Attempts</h4>
                </div>
                <div class="modal-body" id="attempts">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>

{{ link_to('/orders/create', 'New Order') }}&nbsp;{{ link_to("/orders/edit/{$orders->id}", 'Edit Order') }}

@if (!empty($orders))
<h2>Order # {{ $orders->id }}</h2><p>


{{ $orders->court }}<p>
{{ $orders->plaintiff }}v.{{ $orders->defendant }}<p>
Case: {{ $orders->courtcase }}<p>
Reference: {{ $orders->reference }}<p>
@else
<h2>No Order to display!</h2>
@endif
<input type="button" name="view" value="Add Defendants" id="add_defendant" class="btn btn-info btn-xs" /> <div>

     {{ link_to("/documents/upload/{$orders->id}", 'Upload Documents') }}<br>
     {{ link_to("/documents/view/?orderId={$orders->id}", 'View Documents') }}<br>

  </div>

<div>

{{ Form::open(['route' => 'jobs.actions']) }}

    <table>
        <tr>
            <th>Servee</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>History</th>
            <th>Actions <label><input type="checkbox" id="checkAll"/> Select all</label></th>
        </tr>

@if(!empty($verify))

            <tr>
                <td>Verify Documents</td>
                <td>{{ $verify->process }}</td>
                <td>{{ date("m/d/y", strtotime($verify->deadline)) }} </td>
                <td></td>
                <td>{{ Form::checkbox('jobId[]', $verify->job_id) }}</td>
            </tr>

@endif

@if(!empty($recording))
            <tr>
                <td>{{ $recording->defendant }}</td>

                <td> {{ $recordingStatus }} </td>

                @if(!empty($recordingTasks))
                <td>{{ date("m/d/y", strtotime($recordingTasks->deadline)) }} </td>

                @else

                <td></td>

                @endif
                <td></td>
                <td>{{ Form::checkbox('jobId[]', $recording->id) }}</td>
            </tr>
@endif

@if(!empty($filing))

            <tr>

                <td>{{ $filing->defendant }}</td>

                <td> {{ $filingStatus }} </td>

                @if(!empty($filingTasks))
                    <td>{{ date("m/d/y", strtotime($filingTasks->deadline)) }} </td>

                @else

                    <td></td>

                @endif
                <td></td>
                <td>{{ Form::checkbox('jobId[]', $filing->id) }}</td>
            </tr>

@endif

@if(!empty($defendants))

@foreach($servees as $servee)


            <tr>

                <td>{{ $servee->defendant }}</td>

                <td> {{ $defendants[$servee->id]["status"] }} </td>

                @if(!empty($defendants[$servee->id]["due"]))
                    <td>{{ date("m/d/y", strtotime($defendants[$servee->id]["due"])) }} </td>

                @else

                    <td></td>

                @endif
                <td><input type="button" name="view" value="View Attempts" id={{ $servee->id }} class="btn btn-info btn-xs view_data" /></td>
                <td>{{ Form::checkbox('jobId[]', $defendants[$servee->id]["jobId"]) }}</td>
            </tr>

@endforeach

@endif
    </table>
<br>

{{ Form::select('action', $actions) }}
{{ Form::submit('Submit') }}
{{ Form::close() }}

</div>

<a href="{{ URL::previous() }}">Go Back</a>
<div id="dataModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Defendants</h4>
            </div>
            <div class="modal-body" id="add_defendant">
                {{ Form::open(array('route' => 'jobs.store', 'id'=>'defendant_form') )}}
                <div class="names"><input type="text" id="defendants[1][name]" name="defendants[1][name]" required><input type="checkbox" name="personal" id="defendants[1][personal]" name="defendants[1][personal]">Personal Service Required</div>

                <div class="input_fields_wrap"></div>

                <button class="add_defendant_button">Add More Servees</button><br>
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
                    <div id="non-verified" style="display:none">
                                <label for="county">County:</label>
                                <select id="county" name="county">
                                </select>
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
                    {{ Form::label('priority', 'Priority: ') }}
                    {{ Form::select('priority', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
                </div>
                <div id="verified"></div>



                {{ Form::hidden('orders_id', $orders->id) }}

                {{ Form::hidden('company', $orders->company) }}


                    <div><button id="AddDefendant" class="Submit">Submit</button>{{ Form::reset('Reset') }}</div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.view_data').click(function () {
            var serveeId = $(this).attr("id");
            $.ajax({
                method: 'GET', // Type of response and matches what we said in the route
                url: '/attempts/view', // This is the url we gave in the route
                data: {serveeId: serveeId},
                success: function(response) {

                        console.log(response);
                        $('#attempts').html(response);
                        $('#attemptModal').modal("show");
                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        });

    var ss = jQuery.LiveAddress({
        key: '5198528973891423290',
        waitForStreet: true,
        autocomplete: 0,
        submitSelector: "#AddDefendant",
        verifySecondary: true,
        addresses: [{
            street: '#street',
            street2: '#street2',
            city: '#city',
            state: '#state',
            zipcode: '#zipcode'
        }]

    });


    $('#add_defendant').click(function(){
        $('#dataModal').modal("show");


    });
    ss.on("AddressAccepted", function (event, data, previousHandler) {

        if (data.response.chosen) {


            $("#verified").append('<input type="hidden" id="county" name="county" value="' + data.response.chosen.metadata.county_name + '">');

        }
        else {

            getCounty($("#state").val());

            $("#non-verified").show();


        }

        previousHandler(event, data);

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
    }

</script>
@stop
