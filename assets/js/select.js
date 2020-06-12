core.select = {
    handleSelection: function (select, selection) {
        select.on('select2:unselecting', function (event) {
            const data = event.params.args.data;
            selection.append(`<option value="${data.id}">${data.text}</option>`)
        });
    },
    handleMoving: function (buttom, appendTo, selection) {
        $(buttom).on('click', function () {
            if (selection) {
                const items = $(selection);
                $.each(items.val() ? items.val() : [], function (index, id) {
                    const option = items.find(`option[value=${id}]`);
                    const text = option.text();
                    const newOption = new Option(text, id, false, true);

                    appendTo.append(newOption).trigger('change');
                    option.remove();
                });
            }

        });
    },
    handleDelete: function (button, select, selection) {
        $(button).on('click', function () {
            const values = $(select).select2('data');
            $(select).val(null).trigger('change');

            $(values).each(function (index, data) {
                selection.append(`<option value="${data.id}">${data.text}</option>`)
            });
        });
    },
    handleDeleteAll: function (select, selection) {
        const values = $(select).select2('data');
        $(select).val(null).trigger('change');

        $(values).each(function (index, data) {
            selection.append(`<option value="${data.id}">${data.text}</option>`)
        });
    },
    handleSearch: function (input, selection) {
        $(input).on('input', function () {
            const criteria = $(this).val().trim().toLowerCase();

            $(selection).find('option').each(function (index, option) {
                const text = $(option).text().trim().toLowerCase();

                if (text.includes(criteria)) {
                    $(option).show();
                } else {
                    $(option).hide();
                }
            })
        });
    },
    handleMovementBehavior: function (select, selection, buttonMove, buttonDelete, userSelector) {
        core.select.handleSelection(select, selection);
        core.select.handleSearch(userSelector, selection);
        core.select.handleMoving(buttonMove, select, selection);
        core.select.handleDelete(buttonDelete, select, selection);
    }
};
