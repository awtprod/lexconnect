<html>
<body>

        <div>
            The following job has been canceled. Please stop any work on this job and complete a proof of service, if applicable.<br>
            Job ID: {{$job->id}}<br>
            Servee: {{$job->defendant}}<br>
            Address: {{$job->street}},&nbsp;{{$job->city}},&nbsp;{{$job->state}}&nbsp;{{$job->zipcode}}<p>

            This message has been automatically generated. If you have any questions or concerns, please contact us at vendors@lexsend.com. <p>

            Thanks,<br>
            LexSend Team
        </div>
</body>
</html>