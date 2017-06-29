<!DOCTYPE html>
<html>
<head>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>

    <script>
        $(document).ready(function() {



            var ss = jQuery("#attempt-form").LiveAddress({
                key: '5198528973891423290',
                waitForStreet: true,
                autocomplete: 0,
                submitSelector: "#submit",
                verifySecondary: true,
                addresses: [{
                    id: 'attempt-form',
                    street: '.street',
                    street2: '.street2',
                    city: '.city',
                    state: '.state',
                    zipcode: '.zipcode'
                }]

            });

            ss.on("AddressAccepted", function (event, data, previousHandler) {

                if (data.response.chosen) {

                        $("#verified_county").append('<input type="hidden" id="county" name="county" value="' + data.response.chosen.metadata.county_name + '">');
                        $("#non_verified_county").hide();


                }
                else {

                    getCounty($("#state").val());

                    $("#non_verified_county").show();

                }

                previousHandler(event, data);

            });

            ss.deactivate("attempt-form");

            function getCounty(state){
                $.get("{{ url('api/getcounties')}}", { option: state },
                        function(data) {
                            console.log(data);
                            var numbers = $('.county');
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

            $("#attempt-result").change(function () {


                if($("#attempt-result").val()=="non-served") {

                    var form = $(this);

                    $("#non-served").show();

                    ss.deactivate("attempt-form");

                    $("#served").hide();
                }
                if($("#attempt-result").val()=="served"){


                    $("#non-served").hide();
                    $("#served").show();

                }
                else if($("#attempt-result").val()=="attempt"){

                    $("#non-served").hide();
                    $("#served").hide();
                    ss.deactivate("attempt-form");

                }
            });

            $("#serve_type").change(function () {

                if ($("#serve_type").val() == "substitute") {

                    $("#sub-serve-options").show();
                    $("#relationship-select").show();

                }
                else if ($("#serve_type").val() == "corporate") {
                    $("#relationship-select").hide();
                    $("#sub-serve-options").show();

                }
                else {
                    $("#relationship-select").hide();
                    $("#sub-serve-options").hide();
                }

            });

            $('#dataModal').modal("show");


            $("#reason").change(function () {

                    if($("#reason").val()=="MOVED"){

                        $("#new_address").show();
                        $("#reason_other").hide();
                    }
                else if($("#reason").val()=="Other"){

                        $("#new_address").hide();
                        $("#reason_other").show();
                    }
                else if($("#reason").val()=="VACANT"){

                        $("#new_address").hide();
                        $("#reason_other").hide();
                    }
            });

            $("#new_address_given").change(function () {

                $("#new_address_data").toggle();

            });

            $("#location").change(function () {


                if($("#location").val()=="other") {

                    $("#other_location").show();
                    ss.activate("attempt-form");
                }
                else{
                    ss.deactivate("attempt-form");
                    $("#other_location").hide();
                }

            });

            var form = $(this);

            $("#attempt-form").validate({

                rules: {
                    date: "required",
                    time: "required",
                    description: "required",
                    reason: "required",
                    reason_other: "required",
                    Street: "required",
                    City: "required",
                    county: "required",
                    state: "required",
                    Zipcode: "required",
                    served_upon: "required",
                    relationship: "required",
                    hair: "required",
                    gender: "required",
                    height: "required",
                    weight: "required",
                    age: "required"
                },
                submitHandler: function (form) {
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/attempts', // This is the url we gave in the route
                        data: $(form).serialize(), // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            console.log(response);
                            task_table()
                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                            event.preventDefault();

                        }
                    });
                }

            });

            function task_table() {

                $.get("{{ url('api/tasksTable')}}",
                        function (data) {
                            $('#taskTable').html(data);

                        });
            }

        });
    </script>
    <style>
        #attempt
        {
            display:none;
        }
        #served
        {
            display:none;
        }
        .smarty-popup{
            position: fixed;
            padding: 2em;
            left: 50%;
            top: 65%;
            transform: translateX(-50%);
        }
    </style>


</head>
<body>
<h4 class="modal-title">Service Attempt</h4>

    <form id="attempt-form">

    <h1>Add Service Attempt</h1>

        <div>
            <label>Date:</label>
            <input id="date_attempt" name="date" type="date">
        </div>
        <div>
            <label>Time:</label>
            <input id="time_attempt" name="time" type="time">
        </div>
    <div>
        <label>Description:</label>
        <input id="description" name="description" type="text">
    </div>
        <label>Attempt Result: </label>
        <select id="attempt-result" name="attempt-result">
            <option value="attempt">More Attempts Needed</option>
            <option value="served">Defendant Served</option>
            <option value="non-served">Non-Serve</option>
        </select>

    <div id="non-served" hidden>
        <div id="non_serve_reason">
            <label>Reason: </label>
            <select id="reason" name="reason">
                <option value="">Select</option>
                <option value="MOVED">Moved</option>
                <option value="VACANT">Vacant</option>
                <option value="Other">Other(fill in below)</option>
            </select>
        </div>
        <input id="reason_other" name="reason_other" type="text" hidden>

        <div id="new_address" hidden>

                <label>New Address Provided: </label>
                <select id="new_address_given" name="new_address_given">
                    <option value="False" selected>No</option>
                    <option value="True">Yes</option>
                </select>

                    <div id="new_address_data" hidden>
                            <label>Street:</label><input type="text" id="New_Street" name="Street"> &nbsp;
                            <label>Apt/Stuite/Unit:</label><input type="text" id="New_Street2" name="Street2"><br>
                            <label>City:</label><input type="text" id="New_City" name="City">&nbsp;
                            {{ Form::label('state', 'State: ') }}
                            {{ Form::select('state', $states, null, ['id' => 'New_state']) }}
                            <label>Zipcode:</label><input type="text" id="Zipcode" name="New_Zipcode"><br>
                    </div>
    </div>
    </div>

<div id ="served" hidden>

        <label>Type:</label>
        <select id="serve_type" name="serve_type">
            <option value="personal">Personal</option>
            <option value="substitute">Substitute</option>
            <option value="corporate">Corporate</option>
        </select><br>

    <div id="sub-serve-options" hidden>
        <label>Served Upon: </label>
        <input id="served_upon" name="served_upon" type="text"><br>
    </div>
    <div id="relationship-select" hidden>
        <label>Relationship: </label>
        <select id="relationship" name="relationship">
            <option value="">Select</option>
            <option value="CO-RESIDENT">Co-Resident</option>
            <option value="PARENT">Parent</option>
            <option value="SIBLING">Sibling</option>
            <option value="SPOUSE">Spouse</option>
            <option value="CHILD">Child</option>
        </select>
    </div>
        <label>Location: </label>
        <select id="location" name="location">
            <option value="{{$job->street}},&nbsp;@if(!empty($job->street2)),&nbsp;@endif
                            {{$job->city}},&nbsp;{{$job->state}}&nbsp;{{$job->zipcode}}">
                            {{$job->street}},&nbsp;@if(!empty($job->street2)),&nbsp;@endif
                            {{$job->city}},&nbsp;{{$job->state}}&nbsp;{{$job->zipcode}}
            </option>
            <option value="other">Other (fill in below)</option>
        </select>
        <div id="other_location" hidden>
            <label>Street:</label><input type="text" class="street" name="New_Street"> &nbsp;
            <label>Apt/Stuite/Unit:</label><input type="text" class="New_Street2" name="New_Street2"><br>
            <label>City:</label><input type="text" class="city" name="New_City">&nbsp;
            {{ Form::label('state', 'State: ') }}
            {{ Form::select('state', $states, null, ['class' => 'New_state']) }}
            <label>Zipcode:</label><input type="text" class="zipcode" name="New_Zipcode"><br>
        </div>

            <div id="non_verified_county" hidden>
                <label for="county">County:</label>
                <select class="county" name="county">
                </select>
            </div>
            <div id="verified_county"></div>
    <div><br>
        {{ Form::label('gender', 'Gender: ') }}
        {{ Form::select('gender', array('male'=>'male','female'=>'female')) }}
        {{ Form::label('age', 'Age: ') }}
        {{ Form::select('age', array('15-19'=>'15-19','20-24'=>'20-24','25-29'=>'25-29','30-34'=>'30-34','35-39'=>'35-39','40-44'=>'40-44','45-49'=>'45-49','50-54'=>'50-54','55-59'=>'55-59','60-64'=>'60-64','65-69'=>'65-69','over 70'=>'over 70')) }}
        {{ Form::label('race', 'Race: ') }}
        {{ Form::select('race', array('Caucasian'=>'Caucasian','African American/Black'=>'African American/Black','Hispanic'=>'Hispanic','Asian'=>'Asian','Middle Eastern'=>'Middle Eastern','Pacific Islander'=>'Pacific Islander','Native American'=>'Native American')) }}
        {{ Form::label('height', 'Height: ') }}
        {{ Form::select('height', array('4\'6"-4\'8"'=>'4\'6"-4\'8"','4\'9"-4\'11"'=>'4\'9"-4\'11"','5\'0"-5\'2"'=>'5\'0"-5\'2"','5\'3"-5\'5"'=>'5\'3"-5\'5"','5\'6"-5\'8"'=>'5\'8"-5\'8"','5\'9"-5\'11"'=>'5\'9"-5\'11"','6\'0"-6\'2"'=>'6\'0"-6\'2"','6\'3"-6\'5"'=>'6\'3"-6\'5"','6\'6"-6\'8"'=>'6\'6"-6\'8"','6\'9"-6\'11"'=>'6\'9"-6\'11"','Over 7\''=>'Over 7\'')) }}
        {{ Form::label('weight', 'Weight: ') }}
        {{ Form::select('weight', array('under 100 lbs'=>'under 100 lbs','100 lbs-120 lbs'=>'100 lbs-120 lbs','120 lbs-140 lbs'=>'120 lbs-140 lbs','140 lbs-160 lbs'=>'140 lbs-160 lbs','160 lbs-180 lbs'=>'160 lbs-180 lbs','180 lbs-200 lbs'=>'180 lbs-200 lbs','200 lbs-220 lbs'=>'200 lbs-220 lbs','220 lbs-240 lbs'=>'220 lbs- 240 lbs','Over 240 lbs'=>'Over 240 lbs')) }}
    </div>
    <div>
        {{ Form::label('hair', 'Hair: ') }}
        {{ Form::select('hair', array('bald'=>'bald','brown'=>'brown','blonde'=>'blonde','red'=>'red','gray'=>'gray')) }}
        {{ Form::label('beard', 'Beard: ') }}
        {{ Form::checkbox('beard', 'Beard') }}
        {{ Form::label('glasses', 'Glasses: ') }}
        {{ Form::checkbox('glasses', 'Glasses') }}
        {{ Form::label('moustache', 'Moustache: ') }}
        {{ Form::checkbox('Moustache', 'Moustache') }}
    </div>

</div>
    </div>
        <input id="jobId" name="jobId" type="hidden" value="{{ $job->id }}">
        <input id="taskId" name="taskId" type="hidden" value="{{ $taskId }}">
        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
        <input id="submit" type="submit">
        </form>
</body>
</html>
