$(document).ready(function () {
    $('.terms-collection').collection({
        add: `<a class="btn btn-primary" href="#"><i class="fa fa-plus"></i>  Adicionar término </a>`,
        // remove: `<a href="#" class="btn btn-danger mt-collection pull-right"><i class="fa fa-trash"></i></a>`,
        allow_up: false,
        allow_down: false,
        allow_duplicate: false,
        add_at_the_end: true,
        buttonLabel: 'Adicionar unidad',
        name_prefix: 'policy1[terms]',
    });
    $('.conditions-collection').collection({
        add: `<a class="btn btn-primary" href="#"><i class="fa fa-plus"></i> Adicionar condición </a>`,
        // remove: `<a href="#" class="btn btn-danger mt-collection pull-right"><i class="fa fa-trash"></i></a>`,
        allow_up: false,
        allow_down: false,
        allow_duplicate: false,
        add_at_the_end: true,
        buttonLabel: 'Adicionar unidad',
        name_prefix: 'policy1[terms]',
    });
});