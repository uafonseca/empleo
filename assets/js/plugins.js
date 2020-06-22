coreApp.plugins={
    initNewFileInput: function (selector, imageDefault, style = '') {

        let myStyle = style === '' ? 'width: "100px", height: "92px"' : 'width:' + style.width;
        let STYLE_SETTING = 'style="width:{width};height:{height};"',
            img_field = "<img src='value_replace' style='" + myStyle + "' alt='foto'>";


        $(selector).fileinput({
            'defaultPreviewContent': [
                img_field.replace('value_replace', imageDefault)
            ],
            'previewSettings': {
                image: style === '' ? {width: "100px", height: "92px"} : style
            },
            'initialCaption': '&nbsp; Foto Trabajador', // Muestro que esa foto es la original del usuario
            'overwriteInitial': false, // Cuando limpien el preview se vuelve a mostrar el initialCaption
            'dropZoneEnabled':false,
            'browseLabel': '&nbsp;Importar',
            'browseIcon': '<i class="fa fa-upload"></i>&nbsp;',
            'removeLabel': 'Eliminar',
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
                image: '   <img src="{data}" class="file-preview-image" title="{caption}" alt="{caption}" ' + STYLE_SETTING + '>\n'
            }
        });
    }
};