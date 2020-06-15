import '@furcan/iconpicker'

$(document).ready(function () {
    $('#category1_ico').iconpicker({
        placement: "inline",
        title: "Seleccionar el ícono deseado",
        hideOnSelect: true,
    }).on('iconpickerSelected', function (e) {
        // $('.target-icon').val(e.iconpickerInstance.options.fullClassFormatter(e.iconpickerValue));
    });
});