@section('title', 'BAUART REPORTS | Сотрудники')

@extends('layouts.app')

@section('content')

<style>
    /*table { border: 1px solid; }*/
    /*th, td { border: 1px solid; text-align: center; }*/
    .table td, .table th {
        /*display: inline;*/
        padding: 3px 5px !important;
        vertical-align: middle;
        overflow: auto;
        /*white-space: pre-wrap;*/
        /*word-wrap: break-word;*/
    }
    table input {
        border: 1px solid #fff;
        background: transparent;
        color: white;
    }
    @media (max-width: 576px) {
        .table { font-size: 0.7em; }
        .table td {
            padding: 0;
        }
        .table input { max-width: 100px; }
    }
</style>

<div class="container">
    <div class="row">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <table class="table table-bordered table-sm">

            <thead>
            <th scope="col">#</th>
            <th scope="col">Telegram name</th>
            <th scope="col">Имя</th>
            <th scope="col">Фамилия</th>
            <th scope="col">Действие</th>
            </thead>

            <?php $index = 1; ?>

            <?php foreach ($users as $user): ?>
                <form action="{{ route('person-action', [$user['id']]) }}" method="post" id="form-edit-users">
                    @csrf
                    <tbody>
                    <tr>
                        <th scope="row"><?php echo $index++; ?></th>
                        <td><input type="text" name="tg_title_name" value="{{ $user['tg_title_name'] }}"></td>
                        <td><input type="text" name="first_name" value="{{ $user['first_name'] }}"></td>
                        <td><input type="text" name="last_name" value="{{ $user['last_name'] }}"></td>
                        <td>
                            <button class="btn btn-primary " type="submit" name="edit_person" value="true">Изменить</button>
                            <button class="btn btn-danger" type="submit" name="delete_person" value="true">Удалить</button>
                        </td>
                    </tr>
                    </tbody>
                </form>
            <?php endforeach; ?>

        </table>
    </div>
</div>
@endsection

@section('js')
    <script>
        $('button[type=submit]').click(function (e) {

            let name = $(this).attr('name');
            let result = '';

            switch (name) {
                case 'delete_person':
                    result = confirm('Вы уверены что хотите удалить пользователя?')
                    break;
                case 'edit_person':
                    result = confirm('Вы уверены что хотите изменить данные пользователя?')
                    break;
            }

            if (!result) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection
