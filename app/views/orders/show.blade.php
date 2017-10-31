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

            $('.edit_data').click(function () {
                var order_id = $(this).attr("id");
                var token = $('#_token').val();
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/orders/edit', // This is the url we gave in the route
                    data: {orderId: order_id, _token: token },
                    success: function(response) {
                        console.log(response);
                        $('#edit_order').html(response);
                        $('#editModal').modal("show");
                    },
                    error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            });
            $('#editModal').on('hidden.bs.modal', function (e) {
                window.location.reload(true);
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
<td><input type="button" name="view" value="Edit Order" id={{ $orders->id }} class="btn btn-info btn-xs edit_data" /></td><br>
<input id="_token" name="_token" type="hidden" value="{{ csrf_token() }}">
<div id="editModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Order</h4>
            </div>
            <div class="modal-body" id="edit_order">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@else
<h2>No Order to display!</h2>
@endif
<input type="button" name="view" value="Add Defendants" id="add_defendant" class="btn btn-info btn-xs" /> <div>

     {{ link_to("/documents/upload/{$orders->id}", 'Upload Documents') }}<br>
     {{ link_to("/documents/view/?orderId={$orders->id}", 'View Documents') }}<p>

  </div>

<div>

{{ Form::open(['route' => 'jobs.actions']) }}

    @if(count($served)>0)
<h3>Defendants Served:</h3><br>
        @foreach($servees as &$servee)
            <table>
                <tr>
                    <th>Servee</th>
                    <th>Date Served</th>
                    <th>Time Served</th>
                    <th>Manner Served</th>
                    <th>Served Upon</th>
                    <th>Relationship</th>
                    <th>Service Address</th>
                    <th>Proof</th>
                </tr>
                @if(!empty($served[$servee->id]))
                    <td>{{$servee->defendant}}</td>
                    <td>{{date("n/j/y", strtotime($served[$servee->id]["serve"]->date))}}</td>
                    <td>{{date('g:i A', strtotime($served[$servee->id]["serve"]->time))}}</td>
                    <td>{{$served[$servee->id]["serve"]->serve_type}}</td>
                    <td>{{$served[$servee->id]["serve"]->served_upon}}</td>
                    <td>{{$served[$servee->id]["serve"]->realtionship}}</td>
                    <td>{{$served[$servee->id]["serve"]->street}},&nbsp;{{$served[$servee->id]["serve"]->city}},&nbsp;{{$served[$servee->id]["serve"]->state}}&nbsp;{{$served[$servee->id]["serve"]->zipcode}}</td>

                    @if(!empty($served[$servee->id]["proof"]))
                        <td> {{link_to("/documents/show/{$served[$servee->id]["proof"]}", "View Proof",["target"=>"_blank"])}}
                        </td>
                    @else
                        <td></td>
                    @endif
                @endif

                @endforeach
</table><br>
        @endif
            @if(count($not_found)>0)
                <h3>Defendants Not Found/Not Served:</h3><br>
                <table>
                    <tr>
                        <th>Servee</th>
                        <th>History</th>
                    </tr>

                    @if(empty($served[$servee->id]))
                        @foreach($servees as $servee)
                            <tr>

                                <td>{{ $servee->defendant }}</td>
                                <td><input type="button" name="view" value="View Attempts" id={{ $servee->id }} class="btn btn-info btn-xs view_data" /> &nbsp;
                                    {{link_to("/documents/show/{$not_found[$servee->id]["due_diligence"]}", "View Due Diligence Affidavit",["target"=>"_blank"])}}
                                </td>
                            </tr>
                        @endforeach
                    @endif

                </table>

            @endif

@if(!empty($defendants))

                    <h3>Serves In Progress:</h3><br>
                    <table>
                        <tr>
                            <th>Servee</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>History</th>
                            <th>Actions <label></th>
                        </tr>

                            @foreach($servees as $servee)
                            <tr>

                                <td>{{ $servee->defendant }}</td>

                                <td> {{ $defendants[$servee->id]["status"] }} </td>

                                @if(!empty($defendants[$servee->id]["due"]))
                                    <td>{{ date("n/j/y", strtotime($defendants[$servee->id]["due"])) }} </td>

                                @else

                                    <td></td>

                                @endif
                                <td><input type="button" name="view" value="View Attempts" id={{ $servee->id }} class="btn btn-info btn-xs view_data" /></td>
                                @if($defendants[$servee->id]["job"]->completed != NULL OR $defendants[$servee->id]["job"]->status == '3')
                                <td></td>
                                @elseif($orders->status == '0')
                                <td><input type="button" name="{{ $defendants[$servee->id]["job"]->id}}" value="Cancel Job" class="btn btn-info btn-xs cancel_job" /></td>
                                @elseif($defendants[$servee->id]["job"]->status == '0')
                                <td><input type="button" name="view" value="Remove Hold" id={{ $defendants[$servee->id]["job"]->id}} class="btn btn-info btn-xs remove_hold" /> &nbsp: <input type="button" name="view" value="Cancel Job" id={{ $servee->id}} class="btn btn-info btn-xs cancel_job" /></td>
                                @elseif($defendants[$servee->id]["job"]->status == '1')
                                 <td><input type="button" name="view" value="Place On Hold" id={{ $defendants[$servee->id]["job"]->id}} class="btn btn-info btn-xs start_hold" /> &nbsp: <input type="button" name="view" value="Cancel Job" id={{ $servee->id}} class="btn btn-info btn-xs cancel_job" /></td>
                                @endif
                            </tr>
                        @endforeach

                    </table>

                    @endif




@if(!empty($recording))

    <h3>Recording Status: </h3><br>
                <table>
                    <tr>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions <label><input type="checkbox" id="checkAll"/> Select all</label></th>
                    </tr>
            <tr>

                <td> {{ $recordingStatus }} </td>

                @if(!empty($recordingTasks))
                <td>{{ date("m/d/y", strtotime($recordingTasks->deadline)) }} </td>

                @else

                <td></td>

                @endif
                <td>{{ Form::checkbox('jobId[]', $recording->id) }}</td>
            </tr>
                    </table>
@endif

@if(!empty($filing))
<h3>Filing Status: </h3><br>
                <table>
                    <tr>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions <label><input type="checkbox" id="checkAll"/> Select all</label></th>
                    </tr>
            <tr>

                <td>{{ $filing->defendant }}</td>

                <td> {{ $filingStatus }} </td>

                @if(!empty($filingTasks))
                    <td>{{ date("m/d/y", strtotime($filingTasks->deadline)) }} </td>

                @else

                    <td></td>

                @endif
                <td>{{ Form::checkbox('jobId[]', $filing->id) }}</td>
            </tr>
                </table>

@endif


<br>
@if(!empty($filing)OR!empty($recording)OR!empty($defendants))
<div align="center">
{{ Form::select('action', $actions) }}
{{ Form::submit('Submit') }}
{{ Form::close() }}
</div>
@endif

            @if(!empty($verify))

                <h3>Verify Documents:</h3><br>
                <table>
                    <tr>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                    <tr>
                        <td>{{ $verify->process }}</td>
                        <td>{{ date("m/d/y", strtotime($verify->deadline)) }} </td>
                    </tr>
                </table>
            @endif
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
        $('.cancel_job').click(function () {

            console.log($(this).attr("name"));
        });
        $('.start_hold').click(function () {
            actions('add')
        });
        $('.remove_hold').click(function () {
            actions('remove')
        });
        function actions(action){
            console.log(action);
            var _token = $('#_token').val();
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/jobs/actions', // This is the url we gave in the route
                data: {action: action, _token: _token},
                success: function(response) {
                    console.log(response);
                    window.location.reload(true);
                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

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
