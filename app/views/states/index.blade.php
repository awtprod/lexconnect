@extends('layouts.default')
@section('head')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
@section('content')


    <table>
        <tr>
            <th>State</th>
            <th>Abbrevation</th>
            <th>Proof Template</th>
            <th>Mailing Template</th>
        </tr>

            @foreach($states as $state)
                <div><td>
                    {{ $state->name }}
                </div>
                </td>
                <div><td>
                    {{ $state->abbrev }}
                </div>
                </td>
                <td><input type="button" name="view" value="View" id={{ $state->id }} class="btn btn-info btn-xs proof_template" /></td>
                <td><input type="button" name="view" value="View" id={{ $state->id }} class="btn btn-info btn-xs mailing_template" /></td>
                </tr>

        @endforeach
    </table>
    <div id="dataModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><div id="template-title"></div> </h4>
                </div>
                <div class="modal-body"><form id="template_form"> <div id="template_body"></div>
                        <input type="hidden" name="state_id" id="state_id" value="">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <input id="submit" value="submit" type="submit" onclick="myFunction()">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" >Close</button>
                </div>
            </div>
        </div>
    </div>

<a href="{{ URL::previous() }}">Go Back</a>
<script>
    $(document).ready(function() {



        $('.proof_template').click(function () {

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/states/load', // This is the url we gave in the route
                data: {id: $(this).attr("id"), _token: $('#token').val(), type: 'proof'},
                success: function (response) {
                    $('#template_body').summernote('code', response['body']);
                    $('#state_id').val($(this).attr("id"));
                    $('#template-title').html(response['title']);
                    $('#dataModal').modal("show");
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

        });

        $('.mailing_template').click(function () {

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/states/load', // This is the url we gave in the route
                data: {id: $(this).attr("id"), _token: $('#token').val(), type: 'mailing'},
                success: function (response) {
                    $('#template_body').summernote('code', response['body']);
                    $('#state_id').val($(this).attr("id"));
                    $('#template-title').html(response['title']);
                    $('#dataModal').modal("show");
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        });
        function myFunction(e) {
            e.preventDefault();
            var MyDiv2 = document.getElementsByClassName('note-editable');
            m = MyDiv2[0];
            alert(m.innerHTML);
        }
        $('#submits').click(function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/states/save', // This is the url we gave in the route
                data: $(form).serialize(),
                success: function (response) {
                    console.log(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

        });

        $(".modal").on("hidden.bs.modal", function(){
            $('#template_body').summernote('code', '');
            $('#template-title').html(" ");
        });

        });
</script>
@stop
