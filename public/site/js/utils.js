$body = $("body");

$(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
    ajaxStop: function() { $body.removeClass("loading"); }
});

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
            $('#'+parent.id).html('Solicitado')
            $('#splass').fadeIn("slow")
            $('.splass-body p').html('Se ha enviado la solicitud al propietario')
        }
    }).fail(function (error) {
        $('#'+parent.id).html('Solicitado')
        $('#splass').removeClass('splass-success');
        $('#splass').addClass('splass-error');
        $('.splass-body p').html('Ya ha seleccionado esta oferta, se ha enviado un email al propietario de la misma')
        $('#splass').fadeIn("slow")
    })
}