coreApp.plugins={
    initNewFileInput: function (selector, imageDefault, style = '') {

          let img_field = "<img src='value_replace' class='img img-circle' style='width: 145px' alt='foto'>";


        $(selector).fileinput({
            'defaultPreviewContent': [
                img_field.replace('value_replace', imageDefault)
            ],
            'previewSettings': {
                image: style === '' ? {width: "100px", height: "92px"} : style
            },
            'initialCaption': '&nbsp; Foto',
            'overwriteInitial': false, // Cuando limpien el preview se vuelve a mostrar el initialCaption
            'dropZoneEnabled':false,
            'browseLabel': '&nbsp;Seleccionar',
            'browseIcon': '<i class="fa fa-upload"></i>&nbsp;',
            showRemove: false,
            showCancel: false,
            'showUpload': false,
            'layoutTemplates': {
                main1: '' +
                    '<div class="col-md-7 no-padding">' +
                    '{preview}' +
                    '<div class="kv-upload-progress hide"></div></div>\n' +
                    '<div class="col-md-5">' +
                    '       {browse}\n' +
                    '       {remove}\n' +
                    '       {cancel}\n' +
                    '       {upload}\n' +
                    '</div>',
                btnDefault: '<button type="{type}" tabindex="500" style="margin-top: 10px" title="{title}" class="file-input-btn {css}"{status}>{icon}{label}</button>',
                icon: '<i class="fa fa-camera-retro"></i>&nbsp;'
            },
            'previewTemplates': {
                image: '   <img src="{data}" class="file-preview-image" title="{caption}" alt="{caption}" style="width: 145px">\n'
            }
        });
    }
};

// jQuery.extend(jQuery.validator.messages, {
//     required: "This field is required1.",
//     remote: "Please fix this field.",
//     email: "Please enter a valid email address.",
//     url: "Please enter a valid URL.",
//     date: "Please enter a valid date.",
//     dateISO: "Please enter a valid date (ISO).",
//     number: "Please enter a valid number.",
//     digits: "Please enter only digits.",
//     creditcard: "Please enter a valid credit card number.",
//     equalTo: "Please enter the same value again.",
//     accept: "Please enter a value with a valid extension.",
//     maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
//     minlength: jQuery.validator.format("Please enter at least {0} characters."),
//     rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
//     range: jQuery.validator.format("Please enter a value between {0} and {1}."),
//     max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
//     min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
// });