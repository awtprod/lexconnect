<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
    <script>
        $(document).ready(function() {

        });
    </script>
</head>
<body>

<div id="select_task">
<input type="button"  value="Generate Proof" class="btn btn-info btn-xs generate_select"/>&nbsp;
<input type="button"  value="Upload Executed Proof" class="btn btn-info btn-xs upload_select"/>
</div>

<div id="proof" hidden>
<form id="proof_form"> <textarea id="template_body" name="template_body"></textarea>
                        <input type="hidden" name="job_id" id="job_id" value="{{ $job->id }}">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <input type="button" name="save" value="Save Only" id="save" class="btn btn-info btn-xs save" data-dismiss="modal"/>&nbsp;
                        <input type="button" name="generate" value="Save and Generate Proof" id="generate" class="btn btn-info btn-xs save" data-dismiss="modal"/>

</form>
</div>
<div id="upload" hidden>
    <form id="upload_form" method="post" enctype="multipart/form-data">
        <input type="file" name="executed_proof" id="executed_proof" accept="application/pdf"/>
        <input type="hidden" name="job_id" id="job_id" value="{{ $job->id }}">
        <input type="hidden" name="task_id" id="task_id" value="{{ $taskId }}">
        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
        <input type="submit"  class="btn btn-info btn-xs" />

    </form>
</div>

    <script>
        $(document).ready(function() {


            //Validate Data

            $("#upload_form").validate({
                rules: {
                    executed_proof: {
                        required: true,
                        accept: "application/pdf"
                    }
                },
                messages: {
                    executed_proof: {
                        required: "Please upload an executed proof!",
                        accept: "Document must be a pdf!"
                    }
                }
            });

    $("#upload_form").submit(function () {


    var formData = new FormData(this);


    $.ajax({
        url: '/tasks/upload_proof',
        type: 'POST',
        data: formData,
        success: function (data) {
            alert(data)
        },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
        });



            $('.generate_select').click(function (e) {
                e.preventDefault();
                $('#proof').show();
                $('#select_task').hide();
            });
            $('.upload_select').click(function (e) {
                e.preventDefault();
                $('#upload').show();
                $('#select_task').hide();
            });




            $.ajax({

                method: 'POST', // Type of response and matches what we said in the route
                url: '/tasks/proof', // This is the url we gave in the route
                data: {
                    jobId: $('#job_id').val(),
                    _token: $('#token').val(),
                    server: $('#server').find(":selected").text()
                },
                success: function (response) {
                    $('#template_body').summernote('code', response);

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log('poop');

                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

            $('.save').click(function (e) {
                e.preventDefault();

                if($(this).attr("id")=="generate") {
                    var pdf = new jsPDF('p', 'pt', 'letter');
                    // source can be HTML-formatted string, or a reference
                    // to an actual DOM element from which the text will be scraped.
                    source = $("#template_body").val();

                    // we support special element handlers. Register them with jQuery-style
                    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
                    // There is no support for any other type of selectors
                    // (class, of compound) at this time.
                    specialElementHandlers = {
                        // element with id of "bypass" - jQuery style selector
                        '#bypassme': function (element, renderer) {
                            // true = "handled elsewhere, bypass text extraction"
                            return true
                        }
                    };
                    margins = {
                        top: 80,
                        bottom: 60,
                        left: 40,
                        width: 522
                    };
                    // all coords and widths are in jsPDF instance's declared units
                    // 'inches' in this case
                    pdf.fromHTML(
                            source, // HTML string or DOM elem ref.
                            margins.left, // x coord
                            margins.top, { // y coord
                                'width': margins.width, // max width of content on PDF
                                'elementHandlers': specialElementHandlers
                            },

                            function (dispose) {
                                // dispose: object with X, Y of the last line add to the PDF
                                //          this allow the insertion of new lines after html
                                pdf.save('Test.pdf');
                            }, margins);
                }
                var form = $(this);
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/tasks/generate_proof', // This is the url we gave in the route
                    data: {button: $(this).attr("id"), id: $("#job_id").val(), template_body: $("#template_body").val(), _token: $("#token").val()},
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });

            });
            function task_table() {

                $.get("{{ url('api/tasksTable')}}",
                        function (data) {
                            $('#taskTable').html(data);

                        });
            }

        });

    </script>

</body>
</html>