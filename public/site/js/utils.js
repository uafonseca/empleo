function bookMark(id) {
    $.ajax({
        url: '/ajax/bookmark',
        data: {'id':id},
        type: "POST"
    }).done(function (response) {
        // $('#autoremove-'+id).fadeOut()
    }).fail(function (data) {
        console.log("error")
        console.log(data)
    })
}
function applied(id) {
    $.ajax({
        url: '/ajax/applied',
        data: {'id':id},
        type: "POST"
    }).done(function (response) {
        if(response.data){
            console.log("OK")
            $('#applied-btn-'+id).html("Cancelar")
        }

        else{
            console.log("no")
            $('#applied-btn-'+id).html("Aplica ya!")
        }

    }).fail(function (data) {
        console.log("error")
        console.log(data)
    })
}