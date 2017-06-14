<!DOCTYPE html>
<html>
<head>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>

    <script>
        $(document).ready(function() {
            $("#served-task").hide();
            $("#attempt-task").hide();

            $("#attempt_button").click(function () {
                $("#served-task").hide();
                $("#attempt-task").show();
            });
            $("#served_button").click(function () {
                $("#attempt-task").hide();
                $("#served-task").show();
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

            function task_table() {

                $.get("{{ url('api/tasksTable')}}",
                        function (data) {
                            $('#taskTable').html(data);

                        });
            }


            var form = $(this);

            $("#attempt-form").validate({

                rules: {
                    date: "required",
                    time: "required",
                    description: "required"
                },
                submitHandler: function (form) {
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/attempts', // This is the url we gave in the route
                        data: $(form).serialize(), // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            console.log(response);
                            $('#dataModal').modal("hide");
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


            $("#served-form").validate({

                rules: {
                    date: "required",
                    time: "required",
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
                        url: '/serve', // This is the url we gave in the route
                        data: $(form).serialize(), // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            console.log(response);
                            $('#dataModal').modal("hide");
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
    </style>


</head>
<body>
<h4 class="modal-title">Service Attempt</h4>
    <button id="attempt_button">Enter Service Attempt</button>
    <button id="served_button">Defendant Served</button>
<div id ="attempt-task">
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
    <div>
        <label>Non-Serve: </label>
        <input type="checkbox" id="non_serve" value="yes">Note: This will end service for this defendant and generate a Proof of Service.
    </div>

    <input type="submit">
    <input id="jobId" type="hidden" value="{{ $job->id }}">
    <input id="served" type="hidden" value="false">
    <input id="taskId" type="hidden" value="{{ $taskId }}">
    <input id="token" type="hidden" value="{{ csrf_token() }}">

</form>
</div>
<div id ="served-task">
    <form id="served-form">

    <h1>Completed Serve</h1>

    <div>
        <label>Date:</label>
        <input id="date_served" type="date">
    </div>
    <div>
        <label>Time:</label>
        <input id="time_served" type="time">
    </div>
        <label>Type:</label>
        <select id="serve_type">
            <option value="personal">Personal</option>
            <option value="substitute">Substitute</option>
            <option value="corporate">Corporate</option>
        </select>

    <div id="sub-serve-options" hidden>
        <label>Served Upon: </label>
        <input id="served_upon" type="text"><br>
    </div>
    <div id="relationship-select" hidden>
        <label>Relationship: </label>
        <select id="relationship">
            <option value="">Select</option>
            <option value="CO-RESIDENT">Co-Resident</option>
            <option value="PARENT">Parent</option>
            <option value="SIBLING">Sibling</option>
            <option value="SPOUSE">Spouse</option>
            <option value="CHILD">Child</option>
        </select>
    </div>
    <div>
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
        {{ Form::checkbox('beard', 'yes') }}
        {{ Form::label('glasses', 'Glasses: ') }}
        {{ Form::checkbox('glasses', 'yes') }}
        {{ Form::label('moustache', 'Moustache: ') }}
        {{ Form::checkbox('Moustache', 'yes') }}
    </div>

    <input type="submit">
    <input id="jobId" type="hidden" value="{{ $job->id }}">
    <input id="served" type="hidden" value="true">
    <input id="taskId" type="hidden" value="{{ $taskId }}">
    <input id="token" type="hidden" value="{{ csrf_token() }}">
    </form>
</div>
    </div>
</body>
</html>
