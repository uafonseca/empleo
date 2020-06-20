$(function () {
    $(document).on('click', '.cortex-confirm', function (event) {
        const scope = $(this);
        core.dialogs.confirm({
            'event': event,
            'onAccept': function () {
                const mode = scope.attr('data-mode');
                const href = scope.attr('href');

                if (mode !== undefined && mode === 'reload') {
                    window.location.replace(href);
                } else {
                    $.get(href, function (response) {
                        if (response.type === 'success') {
                            toastr.success(response.message);

                            if (response['datatableId']) {
                                $(document).trigger('cortex.delete.complete', {
                                    table: scope.data('table'),
                                    datatableId: response['datatableId']
                                });
                            } else {
                                const table = scope.parents('table').get(0);
                                if (table) {
                                    $(table).DataTable().ajax.reload();
                                }
                            }

                            $(document).trigger('cortex.confirm-response.complete', {
                                response: response
                            });
                        }
                    })
                }
            }
        });
    });

    $(document).on('click', '.btn-loading', function (event) {
        const scope = $(this);
        const element = scope.data('lock');

        cortex.dom.lock(element);
    });
});