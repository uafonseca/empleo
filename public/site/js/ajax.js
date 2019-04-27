$("#send-email-candidate").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    $(this).serializeArray().forEach((object) => {
            data[object.name] = object.value;
    });
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {
            console.log(response.data)
            if(response.data == 'success'){
                var id =$('#id').val();
                $('#modal-'+id).modal('toggle')
                showNotification('success','Mensaje enviado','Se ha enviado un mensaje al usuaro seleccionado.');
                $('#send-email-candidate').find('input:text textarea').val('')
            }else {
                var id =$('#id').val();
                $('#modal-'+id).modal('toggle')
                showNotification('error','Error','Ha ocurrido un error al enviar el mensaje, intentelo más tarde.');
            }
        }).fail(function (data) {
            console.log('fail');
            console.log(data)
        })
})
function findByFilters() {
    let data = {};
    data['category'] = $('#category-filter').value;
    data['location'] = $('#location-filter').value;
    data['gender'] = $('#auto .gender a').html();
    data['experience'] = $('#auto .experience a').html();
    data['post'] = $('#auto .post a').html();
    $('html, body').css("cursor", "wait");
    $.ajax({
        url: '/ajax/filters',
        data: data,
        type: "POST"
    }).done(function (response) {
        $('div#container-jobs').empty();
        $('div#container-jobs').append(response.response);
        $('html, body').css("cursor", "auto");
    }).fail(function (data) {
        console.log(data);
        $('html, body').css("cursor", "auto");
    })
}
function findByFiltersServices() {
    let data = {};
    data['profesion'] = $('#category-filter').value;
    data['location'] = $('#location-filter').value;
    data['gender'] = $('#auto .gender a').html();
    data['experience'] = $('#auto .experience a').html();
    data['post'] = $('#auto .post a').html();
    $('html, body').css("cursor", "wait");
    $.ajax({
        url: '/ajax/filters/services',
        data: data,
        type: "POST"
    }).done(function (response) {
        $('div#container-jobs').empty();
        $('div#container-jobs').append(response.response);
        $('html, body').css("cursor", "auto");
    }).fail(function (data) {
        console.log(data);
        $('html, body').css("cursor", "auto");
    })
}

$("#send-email").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }
    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $('#send-btn').html('<i class="fa fa-circle-o-notch fa-spin"></i> Enviando...');
        $.ajax({
            url: '/mail/sender',
            data: data,
            type: "POST"
        }).done(function (response) {
            $('#send-btn').html('Mensaje Enviado');
            $('#exampleModal').modal('toggle');
            $('#send-btn').html('Enviar');
            console.log(response.data)
        }).fail(function (data) {
            console.log('fail')
            console.log(data)
            $('#send-btn').html('Reintentar');
        })
    }
})

function showModal(id) {
    $('#confirm-delete').modal('show')
    $('#confirm-delete').on('click', "#close", function () {
        $.ajax({
            url: '/ajax/job/remove',
            data: {'id': id},
            type: "POST"
        }).done(function (response) {
            $('#confirm-delete').modal('toggle');
            location.reload()
        }).fail(function (data) {
            console.log('fail')
            console.log(data)
        })

    })
}

$("#qualification").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {

            $('#modal-qualification').modal('toggle');
            location.reload();

        }).fail(function (data) {
            console.log('fail')
            console.log(data)
        })
    }
})
$("#skills-form").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {

            $('#modal-pro-skill').modal('toggle');
            location.reload();

        }).fail(function (data) {
            console.log('fail')
            console.log(data)
        })
    }
})

$("#skills-form-edit").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }
    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {

            $('#modal-pro-skill-edit').modal('toggle');
            location.reload();

        }).fail(function (data) {
            console.log('fail')
            console.log(data)
        })
    }
})


$("#add-experience").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {
            $('#modal-experience-add').modal('toggle');
            location.reload()
        }).fail(function (data) {
            console.log('fail')
            console.log(data)
        })
        $('#modal-experience-add').modal('toggle');
    }
})

function removeEducation(id) {
    $.ajax({
        url: '/ajax/metadata/remove',
        data: {'id': id},
        type: "POST"
    }).done(function (response) {
        // $('#modal-education').modal('toggle');
        $('#div-' + id).remove()
        $('#div1-' + id).remove()
    }).fail(function (data) {
        console.log('fail')
        console.log(data)
    })
}

$("#experience-edit").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        if (data.length > 0) {
            $.ajax({
                url: $(this).attr('action'),
                data: data,
                type: "POST"
            }).done(function (response) {
            }).fail(function (data) {
                console.log('fail')
                console.log(data)
            })
        }
        $('#modal-experience').modal('toggle');
    }

})
$("#form-education").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        if (data.length > 0) {
            $.ajax({
                url: $(this).attr('action'),
                data: data,
                type: "POST"
            }).done(function (response) {
            }).fail(function (data) {
                console.log('fail')
                console.log(data)
            })
        }
        $('#modal-education').modal('toggle');
    }

})
$("#form-education-add").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {
            location.reload()
        }).fail(function (data) {
            console.log('fail')
            console.log(data)
        })
    }
})

$("#social-form").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {
            $('#modal-social').modal('toggle');
        }).fail(function (data) {
            console.log('fail')
        })
    }
})

$("#about-me").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {
            $('#aboutme').html('<p>' + response.data + '</p>')
            $('#modal-about-me').modal('toggle');
        }).fail(function (data) {
            console.log('fail')
        })
    }
})
$("#ajax-skill").on("submit", function (e) {
    e.preventDefault();
    let data = {};
    let errors = []
    $(this).serializeArray().forEach((object) => {
        if (!object.value) {
            errors.push("El campo " + object.name + " no puede estar en blanco")
        } else {
            data[object.name] = object.value;
        }

    });
    if (errors.length > 0) {
        var html = "";
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="list-group-item list-group-item-danger">' + errors[i] + '</li>'
        }
        $('#errors-list').html(html)
        $("#modal-error").modal('show')
    } else {
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            type: "POST"
        }).done(function (response) {
            console.log(response.data.length)
            $('#container-skils').html("")
            for (var i = 0; i < response.data.length; i++) {
                append(response.data[i])
            }
            $('#modal-skill').modal('toggle');
        }).fail(function (data) {
            console.log(data)
        })
    }
})

function append(data) {
    $('#container-skils').append("<a href='#'>" + data + "</a>")
}

function addSkill() {
    var $inputs = $('#ajax-skill :input');
    var col1 = document.createElement("div")
    var group = document.createElement("div")
    var group_input = document.createElement("div")
    var group_text = document.createElement("div")
    var input = document.createElement("input")
    var div3 = document.createElement("div")
    var a = document.createElement("a")
    var i = document.createElement("i")
    var pos = $('#ajax-skill :input').length - 3
    var text = document.createTextNode(pos)

    col1.classList.add("col-sm-9")
    group.classList.add("input-group")
    group_input.classList.add('input-group-prepend')
    group_text.classList.add("input-group-text")
    input.classList.add("form-control")
    div3.classList.add("col-sm-3")
    i.classList.add("fas")
    i.classList.add("fa-times")

    col1.setAttribute("id", "1-" + pos)
    div3.setAttribute("id", "2-" + pos)
    input.setAttribute("name", "skill-" + pos)

    group_text.appendChild(text)

    col1.appendChild(group)
    group.appendChild(group_input)
    group.appendChild(input)
    group_input.appendChild(group_text)
    div3.appendChild(a)
    a.appendChild(i)
    a.onclick = function () {
        $('#1-' + pos).remove()
        $('#2-' + pos).remove()
    }
    document.getElementById("row-skills").appendChild(col1);
    document.getElementById("row-skills").appendChild(div3);
}

function removeItem(item) {
    $('#1-' + item).remove()
    $('#2-' + item).remove()
}
