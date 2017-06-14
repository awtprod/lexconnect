<html>
<head>
<script>
    $(document).ready(function () {

        function task_table() {
            $('#dataModal').modal("show");

            $.get("{{ url('api/tasksTable')}}",
                    function (data) {
                        $('#taskTable').html(data);
                    });
        }
    task_table()
    });
</script>
</head>
</html>