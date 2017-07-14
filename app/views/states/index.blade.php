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
            <th>Non-Serve Template</th>
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
                <td><input type="button" name="view" value="View" id={{ $state->id }} class="btn btn-info btn-xs non_serve_template" /></td>
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
                <div class="modal-body"><form id="template_form"> <textarea id="template_body" name="template_body"></textarea>
                        <input type="hidden" name="state_id" id="state_id" value="">
                        <input type="hidden" name="type" id="type" value="">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <input id="submit" value="Save" type="submit">
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

            var id = $(this).attr("id");

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/states/load', // This is the url we gave in the route
                data: {id: id, _token: $('#token').val(), type: 'proof'},
                success: function (response) {
                    $('#template_body').summernote('code', response['body']);
                    $('#state_id').val(id);
                    $('#type').val("proof");
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

            var id = $(this).attr("id");

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/states/load', // This is the url we gave in the route
                data: {id: id, _token: $('#token').val(), type: 'mailing'},
                success: function (response) {
                    $('#template_body').summernote('code', response['body']);
                    $('#state_id').val(id);
                    $('#type').val("mailing");
                    $('#template-title').html(response['title']);
                    $('#dataModal').modal("show");
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        });
        $('.non_serve_template').click(function () {

            var id = $(this).attr("id");

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/states/load', // This is the url we gave in the route
                data: {id: id, _token: $('#token').val(), type: 'non-serve'},
                success: function (response) {
                    $('#template_body').summernote('code', response['body']);
                    $('#state_id').val(id);
                    $('#type').val("non-serve");
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
        $('#submit').click(function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/states/save', // This is the url we gave in the route
                data: {template_body: $("#template_body").val(), id: $("#state_id").val(), type: $("#type").val(), _token: $("#token").val()},
                success: function (response) {
                    $('#dataModal').modal("hide");
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
