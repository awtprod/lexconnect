<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#attempt_button").click(function(){
                $("#served").hide();
                $("#attempt").show();
            });
            $("#served_button").click(function(){
                $("#attempt").hide();
                $("#served").show();
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
<h1>Enter Service Results:</h1><p>
    <button id="attempt_button">Enter Service Attempt</button>
    <button id="served_button">Defendant Served</button>
<div id ="attempt">

    <h1>Add Service Attempt</h1>

    {{ Form::open(['route' => 'attempts.store']) }}

    <div>
        {{ Form::label('date', 'Date: ') }}
        {{ Form::input('date', 'date') }}
        {{ $errors->first('date') }}
    </div>
    <div>
        {{ Form::label('time', 'Time: ') }}
        {{ Form::input('time', 'time') }}
        {{ $errors->first('time') }}
    </div>
    <div>
        {{ Form::label('description', 'Description: ') }}
        {{ Form::textarea('description') }}
        {{ $errors->first('description') }}
    </div>
    <div>
        {{ Form::label('non-serve', 'Non-Serve: ') }}
        {{ Form::checkbox('non-serve', 'yes') }} Note: This will end service for this defendant and generate a Proof of Service.
        {{ $errors->first('non-serve') }}
    </div>

    {{ Form::hidden('served', 'false') }}
    {{ Form::hidden('taskId', $taskId) }}
    {{ Form::hidden('jobId', $job->id) }}

    <div>{{ Form::submit('Add Attempt') }}{{ Form::reset('Reset') }}</div>
    {{ Form::close() }}
</div>
<div id ="served">
    <h1>Completed Serve</h1>

    {{ Form::open(['route' => 'serve.store']) }}

    <div>
        {{ Form::label('date', 'Date: ') }}
        {{ Form::input('date', 'date') }}
        {{ $errors->first('date') }}
    </div>
    <div>
        {{ Form::label('time', 'Time: ') }}
        {{ Form::input('time', 'time') }}
        {{ $errors->first('time') }}
    </div>
    <div>
        {{ Form::label('served_upon', 'Served Upon: ') }}
        {{ Form::text('served_upon') }}
        {{ $errors->first('served_upon') }}
    </div>
{{ Form::label('relationship', 'Relationship: ') }}
{{ Form::select('relationship', array('DEFENDANT'=>'Defendant','FATHER'=>'Father','MOTHER'=>'Mother','BROTHER'=>'Brother','SISTER'=>'Sister','DAUGHTER'=>'Daugher','SON'=>'Son','CO-RESIDENT'=>'Co-Resident','ROOMMATE'=>'Roommate','REGISTERD AGENT'=>'Registered Agent')) }}
{{ $errors->first('realationship') }}
<div>
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
    <div>
        {{ Form::label('sub-serve', 'Sub-Served?: ') }}
        {{ Form::checkbox('sub-serve', 'yes') }}
        {{ $errors->first('sub-serve') }}
    </div>
    {{ Form::hidden('served', 'true') }}
    {{ Form::hidden('taskId', $taskId) }}
    {{ Form::hidden('jobId', $job->id) }}

            <!-- submit buttons -->
    <div>{{ Form::submit('Defendant Served') }}{{ Form::reset('Reset') }}</div>

    {{ Form::close() }}
</div>
    </div>
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
