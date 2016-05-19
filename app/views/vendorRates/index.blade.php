<html>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) {

            //Load county names into form
            function counties() {
                $.get("{{ url('api/getcounties')}}", {option: $('#state').val()},
                        function (data) {
                            var county = $('#county');
                            county.empty();
                            county.append($("<option></option>")
                                    .attr("value", "")
                                    .text("Select County"));
                            $.each(data, function (key, value) {
                                county.append($("<option></option>")
                                        .attr("value", key)
                                        .text(value));
                            });
                        });
            }

            //Execute counties function on load
            counties();

            //Execute counties function when state is changed
            $('#state').change(counties);

            //Hid/Show rates
            $('.table').change(function () {

                $('.rate').each(function () {

                    if($(this).filter(':checked').val() == 'flat') {

                        $(this).siblings('.flat').show();

                        $(this).siblings('.mileage').hide();

                    }
                    else if($(this).filter(':checked').val() == 'variable'){

                        $(this).siblings('.flat').hide();

                        $(this).siblings('.mileage').show();

                    }
                });

            });
        });

    </script>
    <meta charset="utf-8">
    <style>
        table {
            height:70%;
            width:70%;
            padding:0;
            margin:0;

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
<h1>Modify Rates for Service</h1>
<table>
    <tr>
        <th>State</th>
        <th>County</th>
        <th>Court Run</th>
        <th>Process Service</th>
        <th>Posting</th>
        <th>Page Rate</th>
        <th>Delete County</th>
    </tr>

    <tr>
    {{ Form::open(['route' => 'vendorrates.store']) }}

        @foreach($rates as $rate)
    <tr class="table">
        <div><td>
                {{ $rate->state }}
        </div>
        </td>
        <div><td>
                {{ $rate->county }}

        </div>
        </td>
        <div class="run"><td>
                @if($rate->runFlat != 0 OR ($rate->runFlat == 0 AND $rate->runMileage == 0))

                    <input type="radio" name="run[{{ $rate->id }}]" class="rate" value="flat" checked>Flat
                    <input type="radio" name="run[{{ $rate->id }}]" class="rate" value="variable">Variable<p>

                <div class="flat">
                @else

                        <input type="radio" name="run[{{ $rate->id }}]" class="rate" value="flat">Flat
                        <input type="radio" name="run[{{ $rate->id }}]" class="rate" value="variable" checked>Variable<p>

                <div class="flat" style="display:none">
                @endif

                {{ Form::label('Flat Rate:') }}
                {{ Form::text('revRunFlat['.$rate->id.']',$rate->runFlat) }}<br>
                </div>

                @if($rate->runBase == 0)
                <div class="mileage" style="display:none">
                @else
                <div class="mileage">
                @endif
                {{ Form::label('Base Rate:') }}
                    {{ Form::text('revRunBase['.$rate->id.']',$rate->runBase) }}<br>
                {{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('revRunMileage['.$rate->id.']',$rate->runMileage) }}<br>
                </div>
                {{ Form::label('Rush Surcharge:') }}
                {{ Form::text('revRunRush['.$rate->id.']',$rate->runRush) }}<br>
                {{ Form::label('Same Day Surcharge:') }}
                {{ Form::text('revRunSameDay['.$rate->id.']',$rate->runSameDay) }}<br>
        </div>
        </td>
        <div><td>

                @if($rate->serviceFlat != 0 OR ($rate->serviceFlat == 0 AND $rate->serviceMileage == 0))

                    <input type="radio" name="service[{{ $rate->id }}]" class="rate" value="flat" checked>Flat
                    <input type="radio" name="service[{{ $rate->id }}]" class="rate" value="variable">Variable<p>

                <div class="flat">
                @else

                        <input type="radio" name="service[{{ $rate->id }}]" class="rate" value="flat">Flat
                        <input type="radio" name="service[{{ $rate->id }}]" class="rate" value="variable" checked>Variable<p>

                <div class="flat" style="display:none">
                @endif

                    {{ Form::label('Flat Rate:') }}
                    {{ Form::text('revServiceFlat['.$rate->id.']',$rate->serviceFlat) }}<br>
                </div>

                @if($rate->serviceBase == 0)
                <div class="mileage" style="display:none">
                @else
                <div class="mileage">
                @endif
                {{ Form::label('Base Rate:') }}
                    {{ Form::text('revServiceBase['.$rate->id.']',$rate->serviceBase) }}<br>
                {{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('revServiceMileage['.$rate->id.']',$rate->serviceMileage) }}<br>
                </div>
                {{ Form::label('Rush Surcharge:') }}
                {{ Form::text('revServiceRush['.$rate->id.']',$rate->serviceRush) }}<br>
                {{ Form::label('Same Day Surcharge:') }}
                {{ Form::text('revServiceSameDay['.$rate->id.']',$rate->serviceSameDay) }}<br>
                {{ Form::label('Personal Service Surcharge:') }}
                {{ Form::text('revPersonal['.$rate->id.']',$rate->personal) }}<br>
        </div>
        </td>
        <div><td>

                @if($rate->postFlat != 0 OR ($rate->postFlat == 0 AND $rate->postMileage == 0))

                    <input type="radio" name="post[{{ $rate->id }}]" class="rate" value="flat" checked>Flat
                    <input type="radio" name="post[{{ $rate->id }}]" class="rate" value="variable">Variable<p>

                <div class="flat">
                @else

                        <input type="radio" name="post[{{ $rate->id }}]" class="rate" value="flat">Flat
                        <input type="radio" name="post[{{ $rate->id }}]" class="rate" value="variable" checked>Variable<p>

                <div class="flat" style="display:none">
                @endif
                {{ Form::label('Flat Rate:') }}
                    {{ Form::text('revPostFlat['.$rate->id.']',$rate->postFlat) }}<br>
                </div>
                @if($rate->serviceBase == 0)
                <div class="mileage" style="display:none">

                @else
                <div class="mileage">
                @endif
                {{ Form::label('Base Rate:') }}
                    {{ Form::text('revPostBase['.$rate->id.']',$rate->postBase) }}<br>
                {{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('revPostMileage['.$rate->id.']',$rate->postMileage) }}<br>
                </div>
                {{ Form::label('Rush Surcharge:') }}
                {{ Form::text('revPostRush['.$rate->id.']',$rate->postRush) }}<br>
                {{ Form::label('Same Day Surcharge:') }}
                {{ Form::text('revPostSameDay['.$rate->id.']',$rate->postSameDay) }}<br>
        </div>
        </td>
        <div><td>
                {{ Form::label('# of Pages Included:') }}
                {{ Form::text('revFreePgs['.$rate->id.']',$rate->free_pgs) }}<br>
                {{ Form::label('Rate Per Page(After included pages):') }}
                {{ Form::text('revPageRate['.$rate->id.']',$rate->pg_rate) }}<br>
            </td></div>
        <div><td>{{ link_to("/vendorrates/destroy/{$rate->id}", 'Delete County') }}</td></div>
    </tr>
        {{Form::hidden('rateId['.$rate->id.']', $rate->id)}}
    @endforeach
    <tr class="table">
    <td>
    <div>
        {{ Form::label('') }}
        {{ Form::select('state', $states, null, ['id' => 'state']) }}
        {{ $errors->first('state') }}
    </div>
    </td>
    <td>
    <div class="row">
        <div class="large-9 columns">
            <label for="county"></label>
            <select id="county" name="county">
            </select>
            {{ $errors->first('county') }}
        </div>
        </div>
     </td>
 <div class="run"><td>

                    <input type="radio" name="run[0]" class="rate" value="flat" checked>Flat
                    <input type="radio" name="run[0]" class="rate" value="variable">Variable<p>

                <div class="flat">

                {{ Form::label('Flat Rate:') }}
                {{ Form::text('runFlat') }}<br>
                </div>

                <div class="mileage" style="display:none">

                {{ Form::label('Base Rate:') }}
                    {{ Form::text('runBase') }}<br>
                {{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('runMileage') }}<br>
                </div>
                {{ Form::label('Rush Surcharge:') }}
                {{ Form::text('runRush') }}<br>
                {{ Form::label('Same Day Surcharge:') }}
                {{ Form::text('runSameDay') }}<br>
        </div>
        </td>
        <div><td>

                    <input type="radio" name="service[0]" class="rate" value="flat" checked>Flat
                    <input type="radio" name="service[0]" class="rate" value="variable">Variable<p>

                <div class="flat">

                    {{ Form::label('Flat Rate:') }}
                    {{ Form::text('serviceFlat') }}<br>
                </div>

                <div class="mileage" style="display:none">

                {{ Form::label('Base Rate:') }}
                    {{ Form::text('serviceBase') }}<br>
                {{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('serviceMileage') }}<br>
                </div>
                {{ Form::label('Rush Surcharge:') }}
                {{ Form::text('serviceRush') }}<br>
                {{ Form::label('Same Day Surcharge:') }}
                {{ Form::text('serviceSameDay') }}<br>
                {{ Form::label('Personal Service Surcharge:') }}
                {{ Form::text('personal') }}<br>
        </div>
        </td>
        <div><td>


                    <input type="radio" name="post[0]" class="rate" value="flat" checked>Flat
                    <input type="radio" name="post[0]" class="rate" value="variable">Variable<p>

                <div class="flat">

                {{ Form::label('Flat Rate:') }}
                    {{ Form::text('postFlat') }}<br>
                </div>

                <div class="mileage" style="display:none">

                {{ Form::label('Base Rate:') }}
                    {{ Form::text('postBase') }}<br>
                {{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('postMileage') }}<br>
                </div>
                {{ Form::label('Rush Surcharge:') }}
                {{ Form::text('postRush') }}<br>
                {{ Form::label('Same Day Surcharge:') }}
                {{ Form::text('postSameDay') }}<br>
        </div>
        </td>
        <div><td>
                {{ Form::label('# of Pages Included:') }}
                {{ Form::text('free_pgs') }}<br>
                {{ Form::label('Rate Per Page(After included pages):') }}
                {{ Form::text('pg_rate') }}<br>
            </td></div>
</tr>
</table>
	<div>{{ Form::submit('Add County') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
