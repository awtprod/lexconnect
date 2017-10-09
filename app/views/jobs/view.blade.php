<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
    <script>
        $(document).ready(function() {

        })
    </script>
    <style>
        table {
            height:70%;
            width:70%;
            padding:50px;
            margin:20px;

        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        table#t01 tr:nth-child(even) {
            background-color: #eee;
        }
        table#t01 tr:nth-child(odd) {

            background-color:#fff;
        }
        table#t01 th	{

            background-color: black;
            color: white;
        }
    </style>

</head>
<body>
<form>
<input id="_token" name="_token" type="hidden" value="{{ csrf_token() }}">
<input type="hidden" name="jobId" id="jobId" value="{{$jobs->id}}">
</form>
<h1>Job #{{ $jobs->id }}</h1><p>
    Defendant: {{$jobs->defendant}}<br>
                {{$jobs->street}} @if(!empty($jobs->street2)),&nbsp;{{$jobs->street2}}@endif<br>
                {{$jobs->city}}, &nbsp; {{$jobs->state}}&nbsp; {{$jobs->zipcode}}<p>

    {{ link_to("/documents/view/?jobId={$jobs->id}&_token={$token}", 'View Documents') }}&nbsp;  {{link_to("/tasks/service_documents/{$jobs->id}","Download Service Documents",["target"=>"_blank"])}}<p>
    <br>

<div id="taskTable"></div>
<div id="dataModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Complete Task</h4>
            </div>
            <div class="modal-body" id="complete_task">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        function task_table() {
            var id = $("#jobId").val();

            $.get("{{ url('api/jobsTable')}}",{id:id},
                    function (data) {
                        $('#taskTable').html(data);
                    });
        }

        task_table();

        $('#dataModal').on('hidden.bs.modal', function (e) {
            task_table();
        });

    });
</script>
</body>
</html>