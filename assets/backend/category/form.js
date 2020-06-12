import '@furcan/iconpicker'
$(document).ready(function () {
    $('#category1_ico').iconpicker({
        title: "Seleccionar el ícono deseado",
        hideOnSelect: true,
        inputSearch: true
    }).on('iconpickerSelected', function (e) {
        $('.target-icon').val(e.iconpickerInstance.options.fullClassFormatter(e.iconpickerValue));
    });
});