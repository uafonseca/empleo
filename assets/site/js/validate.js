$(function() {
    $("#job-form").validate({
        // Specify validation rules
        rules: {
            title: "required",
            imageFile: {
                required: true,
                extension: "jpg|jpeg|png"
            },
            category: "required",
            localtion: "required",
            type: "required",
            experience: "required",
            salary_min: {
                required: true,
                digit: true,
                range:[0,Number.MAX_SAFE_INTEGER]
            }
            ,
            salary_max: {
                required: true,
                digit: true,
                min: $('#min').value
            },
        },
        // Specify validation error messages
        messages: {
            title: "Debe especificar un titulo",
            imageFile: "Debe subir solo imagenes",
            category: "Seleccione una categoria",
            type: "Debe seleccionar un tipo",
            experience: "Debe seleccionar un valor de experiencia",
            salary_min: "Debe seleccionar el salario minimo",
            salary_max: "Debe seleccionar un valor correcto para salario maximo"
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});