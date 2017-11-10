<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

</head>
<body>
<h1>Invoices {{$dates}}</h1><div id="filters">&nbsp;@if(!empty($input["vendor"])){{ Form::label('Vendor', 'Vendor: ') }}
    {{ Form::select('Vendor', $vendors, $input["vendor"], ['id' => 'Vendor']) }}@endif{{ Form::label('Vendor', 'Vendor: ') }}
    {{ Form::select('Vendor', $vendors, null, ['id' => 'Vendor']) }}&nbsp;<a id="thisMonth" href="#">This Month</a>&nbsp;<a id="lastMonth" href="#">Last Month</a>&nbsp;<a id="thisYear" href="#">This Year</a>&nbsp;<a id="lastYear" href="#">Last Year</a>&nbsp;Custom Range: <form id="custom">@if(!empty($input["start_date"]))<input id="start_date" type="date" value="{{$input["start_date"]}}">@else<input id="start_date" type="date">@endif - @if(!empty($input["end_date"]))<input id="end_date" type="date" value="{{$input["end_date"]}}">@else<input id="end_date" type="date">@endif<input type="submit"></form> </div><p>


@if (count($earnings)>0)

    <table>
        <tr>
            <th>Job #</th>
            <th>Invoice Date</th>
            <th>Service</th>
            <th>Amount Due</th>
            <th>Payment Method</th>
            <th>Check #</th>
            <th>Date Paid</th>
        </tr>
        @foreach ($earnings as $earning)
            <tr>
                <td>{{ link_to("/jobs/{$earning->job_id}", $earning->job_id) }}</td>
                <td>{{ date("n/j/y", strtotime($earning["created_at"])) }}</td>
                <td>{{  $task[$earning->id]["service"] }}</td>
                <td>{{ $earning->vendor_amt }}</td>
                <td>{{ Form::select('payment_type', $payment_types, $earning->payment_type) }}</td>
                <td>{{ Form::text('check', $earning->check) }}</td>
                <td>@if(!empty($earning->client_paid)){{ date("n/j/y", strtotime($earning->client_paid))}}@endif</td>

                @endforeach
                @else
                    <h2>No invoices to display!</h2>
                @endif
    </tr>
    </table>
            </body>
<script>

        function pay_table(vendor, month, year, start_date, end_date) {
            var token = $('#_token').val();
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/pay_table/', // This is the url we gave in the route
                data: {vendor: vendor, month: month, year: year, start_date: start_date, end_date: end_date, _token: token},
                success: function (response) {
                    console.log(response);
                    $('#payTable').html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        $('#thisMonth').click(function(){pay_table($('#Vendor').val(),'m', '', '', ''); return false;});
        $('#lastMonth').click(function(){pay_table($('#Vendor').val(),'-1', '', '', ''); return false;});
        $('#thisYear').click(function(){pay_table($('#Vendor').val(),'', 'Y', '', ''); return false;});
        $('#lastYear').click(function(){pay_table($('#Vendor').val(),'', '1', '', ''); return false;});
        $("#custom").submit(function (event) {
            event.preventDefault();
            pay_table($('#Vendor').val(),'', '', $('#start_date').val(), $('#end_date').val())
        });
</script>
</html>
