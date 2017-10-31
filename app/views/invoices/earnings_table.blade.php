<html>
<head>

</head>
<body>
<h1>View Active Tasks</h1>


@if (!empty($earnings))

    <table>
        <tr>
            <th>Job #</th>
            <th>Invoice Date</th>
            <th>Task</th>
            <th>Amount</th>
            <th>Date Paid</th>
        </tr>
        @foreach ($earnings as $earning)
            <tr>
                <td>{{ link_to("/jobs/{$earning->job_id}", $earning->job_id) }}</td>
                <td>{{ date("n/j/y", strtotime($earning->created_at)) }}</td>
                <td>{{  }}</td>
                <td>{{ $earning->vendor_amt }}</td>
                <td>{{ date("n/j/y", strtotime($earning->vednor_paid))}}</td>


                @endforeach
                @else
                    <h2>No invoices to display!</h2>
                @endif
    </tr>
    </table>
            </body>
</html>
