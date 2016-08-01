/**
 * Created by Andrew on 7/29/2016.
 */

//Show/Hide first "other" text field
$('.service_documents').change(function(e){

    e.preventDefault();

    if($('.doc_type_select option:selected').val()=='other'){


        $('.doc_other').show();

    }
    else{

        $('.doc_other').hide();
    }
});

//Show/hide additional "other" tex fields

$('.document_wrapper').change(function (e) {

    e.preventDefault();

    $(".supp_doc_type_select").each(function () {
        if ($(this).val() == "other") {

            $(this).next().show();
        }
        else {
            $(this).next().hide();
        }
    });
});

var types = {
    "Notice of Trustee Sale":"Notice of Trustee Sale",
    "AmendedSummons":"Amended Summons",
    "Summons":"Summons",
    "AmendedComplaint":"Amended Complaint",
    "Complaint":"Complaint",
    "NoticeOfPendency":"Notice of Pendency",
    "LisPendens":"Lis Pendens",
    "DeclarationOfMilitarySearch":"Declaration of Military Search",
    "CaseHearingSchedule":"Case Hearing Schedule"
};
var document_wrapper         = $(".document_wrapper"); //Fields wrapper
var add_document_button      = $(".add_document_button"); //Add button ID
var k = 1;


$(add_document_button).click(function(e) { //on add input button click
    e.preventDefault();

    var options = '<option value="">Select Document Type</option>';

    $.each(types, function (key, value) {

        options += '<option value="' + key + '">' + value + '</option>';

    });

    options += '<option value="other">Other (Fill in below)</option>';

    var divContents = '<div class="additional_document">&nbsp;<input type="file" name="documents[' + k + '][file]" class="supp_documents"><div class="doc_type"><select class="supp_doc_type_select" name="documents[' + k + '][type]">' + options + '</select><div class="supp_doc_other" style="display: none"><input type="text" name="documents[' + k + '][other]" class="doc_other_text"></div></div><a href="#" class="remove_field">Remove</a></div>';

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

    //Validate that doc type is selected
    $(".supp_doc_type_select").each(function () {
        $(this).rules('add', {
            required: {
                depends: function (element) {
                    return $('.supp_documents').is(':filled');
                }
            },
            messages: {
                required: "Please select a doc type!"
            }
        });
    });

    //Validate other doc type
    $(".doc_other_text").each(function () {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Please enter a doc type!"
            }
        });
    });
});


$(document_wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('.additional_document').remove();
})
