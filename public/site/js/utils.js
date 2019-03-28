function bookMark(id) {
    $.ajax({
        url: '/ajax/bookmark',
        data: {'id':id},
        type: "POST"
    }).done(function (response) {
    }).fail(function (data) {
    })
}
function applied(id) {
    $.ajax({
        url: '/ajax/applied',
        data: {'id':id},
        type: "POST"
    }).done(function (response) {
        if(response.data){
            $('#applied-btn-'+id).html("Cancelar")
        }
        else{
            $('#applied-btn-'+id).html("Aplica ya!")
        }

    }).fail(function (data) {
    })
}