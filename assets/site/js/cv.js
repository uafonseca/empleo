$(document).ready(function () {
    $('.edit-cv').on('click', function (e) {
        e.preventDefault();
        const scope = $(this);
        coreApp.dialogs.create({url:scope.attr('href')});
    })

});