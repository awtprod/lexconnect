/**
 * Created by Andrew on 7/29/2016.
 */


$(document).ready(function() {


    //show/hide different service options
    $('.services').click(function(){



        if($('input[name=filing]:checked').val() == "filing"){


            $("#filing").slideDown("fast");

        }
        else{
            $("#filing").slideUp("fast");
        }
        if($('input[name=skip]:checked').val() == "skip"){

            $('#skip').slideDown("fast");
        }
        else{

            $('#skip').slideUp("fast");
        }
        if($('input[name=service]:checked').val() == "service"){

            $('.process_service').slideDown("fast");
        }
        else{

            $('.process_service').slideUp("fast");
        }

        if($('input[name=court_run]:checked').val() == "court_run"){

            $("#court_run").slideDown("fast");
        }
        else{

            $("#court_run").slideUp("fast");
        }
    });



    //Validate Data
    $("#create").validate({

        rules: {
            reference: "required"
        }
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
$(document).ready(function() {

    var i = 1;

    //Additonal servees wrapper

    var wrapper = $(".input_fields_wrap"); //Fields wrapper
    var add_button = $(".add_defendant_form"); //Add button ID
    var x = 1;

    $(add_button).click(function (e) { //on add input button click
        e.preventDefault();
        x++;
        $(wrapper).append('<div class="names"><input type="text" name="defendant[' + x + ']" id="defendant[' + x + ']"/><a href="#" class="remove_field">Remove</a></div>'); //add input box

    });

    $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
        e.preventDefault();
        $(this).parent('.names').remove();
        x--;
    });

    var add_skip_defendant_wrapper = $(".skip_defendants"); //Fields wrapper
    var add_skip_defendant_button = $(".add_skip_button"); //Add button ID
    var a = 1;

    $(add_skip_defendant_button).click(function (e) { //on add input button click
        e.preventDefault();
        a++;
        $(add_skip_defendant_wrapper).append('<div class="skip_div"><input type="text" name="skip_defendant[' + a + ']" id="skip_defendant[' + x + ']"/><a href="#" class="remove_field">Remove</a></div>'); //add input box

    });

    $(add_skip_defendant_wrapper).on("click", ".remove_field", function (e) { //user click on remove text
        e.preventDefault();
        $(this).parent('.skip_div').remove();
        a--;
    });

    //Court Run Wrapper
    var add_run_wrapper = $(".add_court_run"); //Fields wrapper
    var add_run_button = $(".add_court_run_button"); //Add button ID
    var y = 1;

    $(add_run_button).click(function (e) { //on add input button click
        e.preventDefault();
        $(add_run_wrapper).append('<div class="supp_court_run"><input type="file" name="run_docs[' + y + ']" class="run_docs"><a href="#" class="remove_field">Remove</a></div>'); //add input box
        y++;

    });

    //SkipTrace Wrapper
    var add_skip_wrapper = $(".add_skip_docs"); //Fields wrapper
    var add_skip_button = $(".add_skip_docs_button"); //Add button ID
    var z = 1;

    $(add_skip_button).click(function (e) { //on add input button click
        e.preventDefault();
        $(add_skip_wrapper).append('<div class="supp_skip"><input type="file" name="skip_docs[' + z + ']" class="skip_docs"><a href="#" class="remove_field">Remove</a></div>'); //add input box
        z++;

    });

    $(add_skip_wrapper).on("click", ".remove_field", function (e) { //user click on remove text
        e.preventDefault();
        $(this).parent('.supp_skip').remove();
        z--;
    });
});

//Service Documents Wrapper
var document_wrapper         = $(".document_wrapper"); //Fields wrapper
var add_document_button      = $(".add_document_button"); //Add button ID
var k = 1;


$(add_document_button).click(function(e) { //on add input button click
    e.preventDefault();


    var divContents = '<div class="additional_document">&nbsp;<input type="file" name="documents[' + k + '][file]" class="supp_documents"><a href="#" class="remove_field">Remove</a></div>';

    $(document_wrapper).append(divContents); //add input box

    k++;
    
        //Validate file type
        $(".supp_documents").each(function () {
        $(this).rules('add', {
            accept: "application/pdf",
            messages: {
                accept: "Document must be a pdf!"
            }
        });
    });
});


$(document_wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('.additional_document').remove();
})
