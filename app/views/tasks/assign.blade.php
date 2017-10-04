<html>
<head>

</head>
<body>

<div>

    Defendant: {{$job->defendant}}<br>
    Address: {{$job->street}}, &nbsp; {{$job->city}}, &nbsp;{{$job->county}},&nbsp; {{$job->state}} &nbsp; {{$job->zipcode}}<br>


    <form id="assign-task">
        {{ Form::label('Assign', 'Assign Server: ') }}
        {{ Form::select('Assign', $vendors, null, ['id' => 'Server']) }}<br>
        <input type="submit">
        <input id="serveeId" name="serveeId" type="hidden" value="{{ $servee->id }}">
        <input id="jobId" name="jobId" type="hidden" value="{{ $job->id }}">
        <input id="taskId" name="taskId" type="hidden" value="{{ $taskId }}">
        <input id="_token" name="_token" type="hidden" value="{{ csrf_token() }}">

    </form>


</div>

<script>
    $(document).ready(function () {

        $('#dataModal').modal("show");

        function task_table(id) {

            $.ajax({
                url: 'api/tasksTable',
                type: 'GET',
                data: {id: id},
                success: function (data) {
                    console.log(data);
                    $('#taskTable').html(data);
                },
                cache: false,
                contentType: false,
                processData: false
            });

        }

        $("#assign-task").submit(function () {


            var formData = new FormData(this);


            $.ajax({
                url: '/tasks/assign',
                type: 'POST',
                data: formData,
                success: function (data) {
                    console.log(data);
                    $('#dataModal').modal("hide");
                    task_table($('#jobId').val())
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