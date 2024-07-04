<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/resources/css/animation_preload.css">
    <link rel="stylesheet" href="/resources/css/style.css">
    <!--    Cкачать бутстрап -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>BAUART REPORTS</title>
</head>

<body>
<nav class="navbar navbar-dark navbar-expand-lg panel-menu">
    <a class="navbar-brand" href="/"><b>BAUART REPORTS</b></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/">Панель менеджера</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/report/date">Отчеты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/persons">Сотрудники</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="http://logout:logout@<?php echo $_SERVER['HTTP_HOST']; ?>">Выйти</a>
            </li>
        </ul>
    </div>
</nav>
