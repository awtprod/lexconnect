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
</form>

<div id="earningsTable"></div>


<script>
    $(document).ready(function() {

        function earnings_table(month,year,start_date,end_date) {
            var token = $('#_token').val();
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/earnings_table/', // This is the url we gave in the route
                data: {month: month, year: year, start_date: start_date, end_date: end_date, _token: token },
                success: function(response) {
                    console.log(response);
                    $('#earningsTable').html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        earnings_table('m','','','');



    });
</script>
</body>
</html>