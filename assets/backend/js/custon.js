$(document).ready(function () {
    $('#messagesDropdown').on('click',function (event) {
        event.preventDefault();
        $.get(Routing.generate('notification_index'),function (data) {
            $('#notifications-container').html(data)
        });
    });
});