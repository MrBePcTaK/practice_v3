@section('title', 'BAUART REPORTS | Отчеты')

@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row distance">

        <div class="col-md-6 col-sm-12 text-center left-block">
            <h5>Выберите дату отчётов:</h5>
            <form method="get" id="form-reports-date" class="distance">
                От:
                <input class="" type="date" value="{{now()->format('Y-m-d')}}" name="start_date_report" id="start_date_report">
                &nbsp;
                До:
                <input class="" type="date" value="{{now()->format('Y-m-d')}}" name="end_date_report" id="end_date_report">

                <br>
                <br>

                <div>
                    <select class="form-control" name="report_user_id" id="report_user_id">
                        <option value="{{null}}"> Все пользователи </option>
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['first_name'] }} {{ $user['last_name'] }}
                                @if(isset($user['tg_username']) && $user['tg_username'] != '') ({{ "@" . $user['tg_username'] }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <br>

                <button class="btn btn-primary" data-assign="get-report" type="button" link="{{ route('report-get') }}">Посмотреть</button>
            </form>

            <div class="general-report bg-light alert">
                <div class="person-report text-center">
                    Выберите дату, за которую отобразить отчёты
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12 text-center">
            <h5 class="mb-3">Добавить отчёт:</h5>

            @if (session('status_save') === 'success')
                <div class="alert alert-success">
                    Отчёт успешно сохранён
                </div>
            @endif

            <div class="alert alert-warning">
                Вы можете сами можете добавить отчёт сотрудника за определённую дату
            </div>

            <form action="{{ route('report-add') }}" method="post">
                @csrf
                <textarea class="add-report form-control" name="adding_report_text" id="" cols="30" rows="10"
                          onresize="true"></textarea>
                <p>Выберите дату: <input class="form-control" type="date" name="report_date"></p>
                <p>
                    Выберите отправителя:
                    <select class="form-control" name="report_user_id" id="">
                        <?php foreach ($users as $user): ?>
                        <option value="{{ $user['id'] }}">{{ $user['first_name'] }} {{ $user['last_name'] }}
                            @if(isset($user['tg_username']) && $user['tg_username'] != '') ({{ "@" . $user['tg_username'] }}) @endif
                        </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <button class="btn btn-primary float-left" name="submit" type="submit" onclick="confirm('Вы действительно хотите добавить отчет за сотрудника?')">
                    Добавить
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
