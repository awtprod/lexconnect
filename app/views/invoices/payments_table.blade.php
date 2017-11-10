<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

</head>
<body>
<h1>Invoices {{$dates}}</h1><div id="filters">&nbsp;<a id="thisMonth" href="#">This Month</a>&nbsp;<a id="lastMonth" href="#">Last Month</a>&nbsp;<a id="thisYear" href="#">This Year</a>&nbsp;<a id="lastYear" href="#">Last Year</a>&nbsp;Custom Range: <form id="custom">@if(!empty($input["start_date"]))<input id="start_date" type="date" value="{{$input["start_date"]}}">@else<input id="start_date" type="date">@endif - @if(!empty($input["end_date"]))<input id="end_date" type="date" value="{{$input["end_date"]}}">@else<input id="end_date" type="date">@endif<input type="submit"></form> </div><p>


@if (count($earnings)>0)

    <table>
        <tr>
            <th>Order #</th>
            <th>Reference #</th>
            <th>Invoice Date</th>
            <th>Service</th>
            <th>Amount</th>
            <th>Date Paid</th>
            <th>Invoice</th>
        </tr>
        @foreach ($earnings as $earning)
            <tr>
                <td>{{ link_to("/orders/{$earning->order_id}", $earning->order_id) }}</td>
                <td>{{$task[$earning->id]["reference"] }}</td>
                <td>{{ date("n/j/y", strtotime($earning["created_at"])) }}</td>
                <td>{{  $task[$earning->id]["service"] }}</td>
                <td>{{ $earning->client_amt }}</td>
                <td>@if(!empty($earning->client_paid)){{ date("n/j/y", strtotime($earning->client_paid))}}@endif</td>
                <td>@if(!empty($earning->doc_id)){{link_to("/documents/show/{$earning->doc_id}", "View Invoice",["target"=>"_blank"])}}@endif</td>

                @endforeach
                @else
                    <h2>No invoices to display!</h2>
                @endif
    </tr>
    </table>
            </body>
<script>

        function earnings_table(month, year, start_date, end_date) {
            var token = $('#_token').val();
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/payments_table/', // This is the url we gave in the route
                data: {month: month, year: year, start_date: start_date, end_date: end_date, _token: token},
                success: function (response) {
                    console.log(response);
                    $('#earningsTable').html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        $('#thisMonth').click(function(){earnings_table('m', '', '', ''); return false;});
        $('#lastMonth').click(function(){earnings_table('-1', '', '', ''); return false;});
        $('#thisYear').click(function(){earnings_table('', 'Y', '', ''); return false;});
        $('#lastYear').click(function(){earnings_table('', '1', '', ''); return false;});
        $("#custom").submit(function (event) {
            event.preventDefault();
            earnings_table('', '', $('#start_date').val(), $('#end_date').val())
        });
</script>
</html>
