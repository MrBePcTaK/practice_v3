@section('title', 'BAUART REPORTS')

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row distance">
            <div class="col-md-6 col-sm-12">
                <div class="logo">
                    <div class="rectangle">
                        <span class="text-company">BAUART</span>
                        <span class="text-group">GROUP</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-12 col-md-6 text-center">

                <div class="col-sm-12 col-md-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                        </div>
                    @endif
                </div>

                @if ($wait_users)
                    <div class="alert alert-danger" style="font-size: 0.85em;">
                        <span>Ожидают подтверждения:</span>
                        <button class="btn btn-primary" id="open-access-list">Показать</button>
                    </div>

                    <div class="block-access-list">
                        <form action="{{ route('access-change') }}" method="post">
                            @csrf
                            <ul class="list-group access-list">

                            </ul>
                        </form>
                    </div>
                @endif


                <div class="alert alert-info" style="font-size: 0.85em;">
                    <span>Сотрудники, от которых ожидается отчёт: </span>
                    <button class="btn btn-primary" id="open-tracked-list">Показать</button>
                </div>

                <div class="block-tracked-list">
                    <form action="{{ route('person-change-status') }}" id="form-track-list">
                        <ul class="list-group track-list">
                            @foreach($users as $user)
                                @if(!empty($user->first_name) && !empty($user->last_name))
                                    <li class="list-group-item">
                                        <input data-href="{{ route('person-change-status') }}" class="check-active-user" type="checkbox" id="{{ $user->id }}" @if($user->active == 1) checked @endif>
                                        <label for="@if($user->id) @endif">
                                            {{ $user->first_name . PHP_EOL . $user->last_name . " (@" . $user->tg_title_name . ")" }}
                                        </label>
                                    </li>
                                @else
                                    <li class="list-group-item">
                                        <input data-href="{{ route('person-change-status') }}" class="check-active-user" type="checkbox" id="{{ $user->id }}"
                                               @if($user->active == 1) checked @endif>
                                        <label for="{{ $user->id }}">{{ @$user->tg_title_name }}</label>
                                    </li>
                                @endif
                            @endforeach

                            <li class="list-group-item">
                                <button type="button" class="btn btn-primary m-auto d-block" id="accept_active_users">Применить</button>
                            </li>
                        </ul>

                    </form>
                </div>

                @if (isset($debtors) && !$debtors->isEmpty())
                    <h4 style="font-size: 1.5em;" class="distance">Ожидание отчета:</h4>

                    <ul class="list-group">
                        @foreach ($debtors as $debtor)
                            <li class="list-group-item list-group-item-action list-group-item-warning">
                                <b>
                                    {{ $debtor['tg_title_name'] . ' (' . $debtor['first_name'] . ' ' . $debtor['last_name'] . ')' }}
                                </b>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="col-sm-12 col-md-6">
                <h4 class="text-center text-general-report">Общий отчет за сегодня:</h4>

                @if(isset($reports) && !$reports->isEmpty())

                    <div class="general-report bg-light alert">
                        <div class="person-report">
                            @foreach ($reports as $report)
                                <form class="form-report">
                                    <p class="alert alert-info text-center">
                                        <span class="report-title"><b>Сотрудник:</b></span>
                                        {{ $report->user->first_name . ' ' . $report->user->last_name . ' (@' . $report->user->tg_username . ')' }}
                                    </p>

                                    <h6 class="report-title"><b>Дата отчета:</b></h6>
                                    <p>{{ $report->created_at }}</p>

                                    <h6 class="report-title"><b>Содержание отчета:</b><br></h6>
                                    <p class="report report-content-{{$report->id}}">
                                        {{ $report->report }}
                                    </p>
                                    <button type="button" id="edit-report-{{ $report->id }}" class="btn btn-primary btn-edit-report">Изменить</button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                @else
                    <p class="text-center">Сегодня не было отправленных отчётов...</p>
                    <section>
                        <div class='sk-circle-bounce'>
                            <div class='sk-child sk-circle-1'></div>
                            <div class='sk-child sk-circle-2'></div>
                            <div class='sk-child sk-circle-3'></div>
                            <div class='sk-child sk-circle-4'></div>
                            <div class='sk-child sk-circle-5'></div>
                            <div class='sk-child sk-circle-6'></div>
                            <div class='sk-child sk-circle-7'></div>
                            <div class='sk-child sk-circle-8'></div>
                            <div class='sk-child sk-circle-9'></div>
                            <div class='sk-child sk-circle-10'></div>
                            <div class='sk-child sk-circle-11'></div>
                            <div class='sk-child sk-circle-12'></div>
                        </div>
                    </section>
                @endif

            </div>

        </div>

    </div>
@endsection

@section('js')
    <script>
        $("#open-access-list").click(function () {

            let url = "{{ route('users-wait-access') }}"

            $.ajax({
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                success: function (result) {

                    let ul = $('.access-list');
                        ul.empty();

                    if (typeof result !== 'undefined' && result !== '' && result.length !== 0) {

                        let tg_title_name = "";
                        let tg_username = "";

                        $.each(result, function (i) {

                            tg_title_name = result[i].tg_title_name;
                            tg_username = result[i].tg_username;

                            ul.append(`<li class="list-group-item">${tg_title_name} (${tg_username}) <input name="users[]" type="checkbox" value="${result[i].id}"></li>`);
                        });

                        ul.append('<li class="list-group-item"><button class="btn btn-primary" type="submit" name="submit" value="submit">Разрешить доступ</button></li>')
                    }
                    else {
                        ul.append('<li class="list-group-item">Нет пользователей ожидающих доступа</li>')
                    }

                    $( ".access-list" ).slideToggle('active');

                }, error: function (err) {
                    console.log(err);
                }
            });
        });
    </script>
@endsection
