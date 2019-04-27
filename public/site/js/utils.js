$body = $("body");

$(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
    ajaxStop: function() { $body.removeClass("loading"); }
});
function showNotification(type,header,body){
    if(type =='success' ){
        $('#splass-s .splass-header #type-success').html(header)
        $('#splass-s .splass-body p').html(body)
        $('#splass-s').fadeIn('slow')
    }else if (type == 'error'){
        $('#splass-e .splass-header #type-error').html(header)
        $('#splass-e .splass-body p').html(body)
        $('#splass-e').fadeIn('slow')
    }
}
$(document).ready(function () {
    setTimeout(
        function()
        {
            $('.publicity').fadeIn("slow")
        }, Math.floor((Math.random() * 10) + 1) * 1000);
    $('#close-footer-pub').click(function () {
        $('.publicity').fadeOut("slow")
    })
})
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
function contrata(id,parent) {
    $('#'+parent.id).html('Enviando...')
    $.ajax({
        url:'/ajax/contrata',
        data:{'id':id},
        type:'POST'
    }).done(function (response) {
        if(response.data == 'success'){
            showNotification('success','Notificación','Se ha enviado una solicitud');
            $('#'+parent.id).html('Solicitado')
        }
    }).fail(function (error) {
        // console.log(error)
        $('#'+parent.id).html('Solicitado')
        showNotification('error','Error','Ya ha solicitado este servicio');
    })
}