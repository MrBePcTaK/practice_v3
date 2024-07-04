$(document).ready(function () {

    addEventForButtonEdit();

    $('button[data-assign=get-report]').click(function () {

        let url = $(this).attr('link');

        let start_date = document.getElementById('start_date_report').value;
        let end_date = document.getElementById('end_date_report').value;
        let user_id = document.getElementById('report_user_id').value;

        // if (date !== '') {

            $.ajax({
                type: 'post',
                url: url,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    user_id: user_id,
                },
                success: function (reports) {
                    console.log(reports);

                    if (reports !== null || reports !== '') {

                        if (reports.length !== 0) {
                            $('.person-report').empty()

                            $.each(reports, function (index, value) {

                                value.report = value.report.replace("\n", "<br>");

                                $('.person-report').append($(`<form class="form-report">
                                    <p class="alert alert-info text-center">
                                        <span class="report-title"><b>Сотрудник:</b></span>
                                        ${value.user.first_name} ${value.user.last_name} (@${value.user.tg_username})
                                    </p>

                                    <h6 class="report-title"><b>Дата отчета:</b></h6>
                                    <p>${value.created_at}</p>

                                    <h6 class="report-title"><b>Содержание отчета:</b><br></h6>
                                    <p class="report report-content-${value.id}">
                                        ${value.report}
                                    </p>
                                       <button type="button" id="edit-report-${value.id}" class="btn btn-primary btn-edit-report" data-assign="edit">Изменить</button>
                                </form>`));
                            });
                            addEventForButtonEdit();

                        } else {
                            $('.person-report').empty().append('За этот день не было отчётов');
                        }
                    }
                }, error: function (err) {

                    $('.error').remove();
                    let error = $('<div class="alert alert-danger error">Дата не выбрана</div>');
                    $('.left-block').prepend(error);
                }
            });
        // }
    });

    $('#open-tracked-list').click(function () {
        $(".track-list").slideToggle('active');
    });

    $('.check-active-user').click(function () {

        let status = $(this).is(":checked");
        let user_id = $(this).attr('id');
        let url = $(this).attr('data-href');

        $.ajax({
            type: 'post',
            url: url,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                status: status,
                user_id: user_id,
            },
            success: function (result) {

                console.log(result);
                // document.location.reload();
            }, error: function (err) {
                console.log(err);
            }
        });
    });

    $('#accept_active_users').click(function () {

        document.location.href = '/';
    });
});

function addEventButtonSave() {

    $('.form-report').on('click', 'a', function () {

        let attr_button = $(this).attr('data-assign').split('-');
        let id = attr_button[1];
        let type = attr_button[0];

        let textarea = $('.edit-report');
        let textarea_text = textarea.text();
        let report_content = $('.report-content-' + id);
            report_content.text(textarea_text);

        switch (type) {
            case 'edit_save':

                $.ajax({
                    type: 'post',
                    url: '/reports/report-edit',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        id: id,
                        text: textarea.val(),
                    },
                    success: function (result) {

                        if (result) {
                            report_content.text(textarea.val());
                        }
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });

                break;
            // case 'edit_cancel':
            //     textarea.remove();
            //     report_content.text(textarea_text);
            //     break;
        }

        let isset_btn = $(`button[id=edit-report-${id}]`);

        if (isset_btn.length == 0) {
            report_content.parent().append($('<button type="button" id="edit-report-' + id + '" class="btn btn-primary btn-edit-report" data-assign="edit">Изменить</button>'));
        }
        $('.block-edit-btn').remove();

    });
}

function addEventForButtonEdit() {
    $('.form-report').on('click', 'button', function () {

        let quest = confirm('Вы действительно хотите изменить этот отчёт?');

        if (quest == true) {

            /* Для изменения получаем информацию о выбранном отчёте */

            let cur_report = $(this).prev();
            let cur_report_text = cur_report.text().trim();
            let cur_report_id = this.id.match(/\d+/);
            let cur_form_edit = cur_report.parent();

            /* Проверяем есть ли открытые textarea в формах, чтобы их закрыть */

            let prev_textarea = $('.edit-report');
            let prev_form_edit = prev_textarea.parent().parent();
            let prev_report_id = prev_textarea.attr('id');

            if (prev_textarea.length !== 0) {
                let textarea_text = prev_textarea.text();
                prev_textarea.parent().text(textarea_text);
                $('.block-edit-btn').remove();

                prev_form_edit.append($('<button type="button" id="edit-report-' + prev_report_id + '" class="btn btn-primary btn-edit-report" data-assign="edit">Изменить</button>'));
            }

            /* Вставляем в конец формы кнопку "Сохранить" */

            cur_report.text('');
            cur_report.append($('<textarea id="' + cur_report_id + '" class="edit-report form-control">' + cur_report_text + '</textarea>'));
            cur_form_edit.append(
                $('<div class="block-edit-btn">' +
                    '<a class="btn btn-success mr-3 text-white" data-assign="edit_save-' + cur_report_id + '" type="button">Сохранить</a>' +
                    '</div>'));

            /* Удаляем кнопку "Изменить" и весим событие на "Сохранить" */

            $(this).remove();
            addEventButtonSave();
        }
    });
}
