@extends('layouts.default')

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
<script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
<script>


    $(document).ready(function() {




        //Validate Data
        $("#create").validate({

            rules: {
                plaintiff: "required",
                'documents[0][text]': "required",
                court: "required"
            },
        });

        $(".doc_type_select").rules('add', {
            required: {
                depends: function (element) {
                    return $('.documents').is(':filled');
                }
            },
            messages: {
                required: "Please select a doc type!"
            }
        });

        $(".documents").rules('add', {
            accept: "application/pdf",
            messages: {
                required: "Document must be a pdf!"
            }
        });

    });


</script>
@section('content')
<h1>Upload Documents</h1>

{{ Form::open(array('route'=>'documents.storeDocuments','files'=>true, 'id'=>'create')) }}

<div class="service_documents">

<input type="file" name="documents[0][file]" class="documents">&nbsp;

<select name="documents[0][type]" class="doc_type_select">
    <option value="">Select Document Type</option>

    @foreach($documents as $document)

        <option value="{{ $document[0] }}">
            {{ $document[0] }}
        </option>
    @endforeach

    <option value="other">Other (Fill in below)</option>
</select>
<div class="doc_other" style="display: none"><input type="text" name="documents[0][text]" class="doc_other_text"><br></div>
<br>

<div class="document_wrapper"></div>

<button class="add_document_button">Add More Documents</button>

</div>

@foreach($documents as $document)
    {{  '<input type="hidden" name="document_types[]" value="'. $document[0]. '">' }}
@endforeach
<p>

    {{ Form::hidden('orderId', $orderId) }}
    <!-- submit buttons -->
    {{ Form::submit('Upload Documents') }}

{{HTML::script('/js/documents.js')}}

@stop
