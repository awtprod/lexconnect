<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/3.4/jquery.liveaddress.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {


                    var ss = jQuery("#attempt-form").LiveAddress({
                        key: '5198528973891423290',
                        waitForStreet: true,
                        autocomplete: 0,
                        submitSelector: "#submit",
                        verifySecondary: true,
                        addresses: [{
                            id: 'locate-task',
                            street: '.street',
                            street2: '.street2',
                            city: '.city',
                            state: '.state',
                            zipcode: '.zipcode'
                        }]

                    });
                });
        </script>
</head>
<body>
<div>

    Defendant: {{$job->defendant}}<br>
    <b>Previously Attempted Addresses: </b><p>

    {{$attempt = 1}}
    @foreach($jobs as $address)
        Attempt # {{$attempt}}<br>
        Address: {{$address->street}}, &nbsp; {{$address->city}}, &nbsp; {{$address->state}} &nbsp; {{$address->zipcode}}<br>
        Reason: {{$address->reason}}<p>
        {{$attempt++}}
    @endforeach

        <select id="locate" name="locate">
            <option value="">Select Result</option>
            <option value="0">Enter New Address</option>
            <option value="3">Non-Serve/Unable to Locate</option>
        </select>

    <form id="locate-task">

        <div id="new" hidden>
            <input type="checkbox" name="personal" id="personal">Personal Service Required<br>
            <input type="checkbox" name="additional" id="additional">Additional Servee<br>
            <label>Street:</label><input type="text" id="Street" name="Street"> &nbsp;
            <label>Apt/Stuite/Unit:</label><input type="text" id="Street2" name="Street2"><br>
            <label>City:</label><input type="text" id="City" name="City">&nbsp;
            {{ Form::label('state', 'State: ') }}
            {{ Form::select('state', $states, null, ['id' => 'state']) }}
            <label>Zipcode:</label><input type="text" id="Zipcode" name="Zipcode"><br>
        </div>

        <div id="non-serve" hidden>
            Declaration of Due Diligence: <input type="file" name="executed_due_diligence" id="executed_due_diligence" accept="application/pdf"/>

        </div>

        <input type="submit">
        <input id="serveeId" type="hidden" value="{{$servee->id}}">
        <input id="taskId" type="hidden" value="{{ $taskId }}">
        <input id="jobId" type="hidden" value="{{ $job->id }}">
        <input id="token" type="hidden" value="{{ csrf_token() }}">

    </form>


</div>

<script>
    $(document).ready(function () {

        $("#locate").change(function () {

            if($("#locate").val()=="0"){
                $("#new").show();
                $("#non-serve").hide();
            }
            if($("#locate").val()=="3"){
                $("#new").hide();
                $("#non-serve").show();
            }
            else {
                $("#new").hide();
                $("#non-serve").hide();
            }

        });


        $('#dataModal').modal("show");

        function task_table() {

            $.get("{{ url('api/tasksTable')}}",
                    function (data) {
                        $('#taskTable').html(data);

                    });
        }

        $("#locate-task").submit(function () {


            var formData = new FormData(this);


            $.ajax({
                url: '/tasks/locate',
                type: 'POST',
                data: formData,
                success: function (data) {
                    alert(data)
                },
                cache: false,
                contentType: false,
                processData: false
            });

            return false;
        });
    });
</script>
</body>
</html>
