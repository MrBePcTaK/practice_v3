<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
{{--    <link rel="stylesheet" type="text/css" href="{{ asset("/css/menu.css") }}" />--}}
{{--    <script src="{{ asset("/js/menu.js") }}"></script>--}}
    <title>Menu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin-top: 70px;
            font-size: 20px;
            font-family: 'Roboto', sans-serif;
            color: var(--tg-theme-text-color);
            background: var(--tg-theme-secondary-bg-color);
            width: 100%;
            text-align: center;
        }

        header {
            position: fixed;
            top: 0;
            width: 100%;
            height: 60px;
            max-height: 60px;
            background: var(--tg-theme-bg-color);
            border-bottom: 2px solid var(--tg-theme-secondary-bg-color);
        }

        .content {
            margin: 10px;
            padding: 10px;
            border: 0;
            border-radius: 10px;
            background: var(--tg-theme-bg-color);
        }

        body::-webkit-scrollbar {
            display: none;
            /*position: absolute;*/
            /*overflow: overlay;*/
            /*margin-top: 60px;*/
            /*display: block;*/
            /*overflow: auto;*/
            /*width: 5px;*/
            /*height: 8px;*/
            /*background-color: transparent;*/
        }

        body::-webkit-scrollbar-thumb {
            /*display: block;*/
            /*overflow: auto;*/
            /*width: 5px;*/
            /*height: 8px;*/
            /*background: #fff;*/
            /*border-radius: 5px;*/
        }

        h1 {
            margin-top: 30px;
            margin-bottom: 10px;
        }

        button {
            text-align: center;
            font-weight: 600;
            border: 2px solid;
            border-radius: 7px;
            cursor: pointer;
            transition: all 500ms ease;
            color: var(--tg-theme-button-color);
            background: var(--tg-theme-bg-color);
            border-color: var(--tg-theme-button-color);
        }

        button:hover {
            color: var(--tg-theme-text-color);
            background: var(--tg-theme-button-color);
        }

        #lunch_menu_button, #main_menu_button {
            margin-top: 5vh;
            margin-left: 50px;
            margin-right: 50px;
            padding: 15px;
            height: 70px;
            width: 220px;
            font-size: 22px;
        }

        #lunch_menu, #main_menu, #order_page {
            display: none;
        }

        #back_button, #order_button {
            align-items: center;
            width: 40px;
            height: 40px;
            margin: 10px;
        }

        #page {
            font-size: 28px;
            font-weight: 500;
        }

        .header_element {
            align-self: center;
        }

        .secondary {
            background: var(--tg-theme-bg-color);
        }

        .category {
            padding-top: 10px;
            padding-bottom: 20px;
            text-align: left;
        }

        .category_name {
            font-size: 24px;
            padding-left: 10px;
        }

        .wrapper {
            display: grid;
        }

        .product {
            display: grid;
            grid-template-columns: auto 100px;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .product_name {
            font-size: 20px;
            text-align: left;
        }

        .price {
            font-size: 18px;
            text-align: center;
            align-self: end;
            margin-bottom: 10px;
        }

        .description {
            font-size: 16px;
            display: inline;
        }

        .hint {
            font-size: 14px;
            color: var(--tg-theme-hint-color);
            display: inline;
        }

        .add_button {
            font-size: 16px;
            height: 35px;
            min-width: 100%;
        }

        .buttons_area {
            height: 35px;
        }

        .child_buttons {
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            align-items: center;
            display: none;
            grid-template-columns: 1fr 1fr 1fr;
        }

        .button_child {
            font-weight: 800;
            font-size: 28px;
            line-height: 20px;
            width: 31px;
            height: 31px;
        }

        .cost_area {
            display: grid;
            grid-template-columns:1fr 1fr;
            border-top: 2px solid var(--tg-theme-secondary-bg-color);
            padding: 15px 10px 10px 10px;
        }

        .order_item {
            display: grid;
            grid-template-columns: auto 40px 70px;
            padding: 10px 0 10px 0;
        }
    </style>
</head>
<header>
    <div class="header">
        <div class="wrapper" style="grid-template-columns: 60px auto 60px;">
            <div class="header_element">
                <button id="back_button" style="font-size: 32px; display: none">↩</button>
            </div>
            <div class="header_element">
                <p id="page">Главная</p>
            </div>
            <div class="header_element">
                <button id="order_button" style="font-size: 24px">🛒</button>
            </div>

        </div>
    </div>
</header>
<body>
    <div class="content">
        <div id="main">
            <h1>Меню</h1>
            <p>Выберите меню-набор:</p>
            <button id="lunch_menu_button">Бизнес-ланч</button>
            <button id="main_menu_button">Основное меню</button>
        </div>
        <div id="lunch_menu" class="secondary">
            @if(is_null($menu))
                <p style="font-size: 24px; padding-top: 50px; padding-bottom: 50px">На сегодня нет позиций в меню</p>
            @else
                @foreach($menu as $categoryName => $category)
                    <div class="category">
                        <p class="category_name">{{ $categoryName }}</p>
                        <div class="wrapper">
                            @foreach($category as $product)
                                <div class="product">
                                    <div>
                                        <p class="product_name">{{ $product->{'name'} }}</p>
                                        @if($product->{'ingredients'} != '')
                                            <p class="description">Состав: </p>
                                            <p class="hint">{{ $product->{'ingredients'} }}</p><br>
                                        @endif
                                        @if($product->{'weight'} != '')
                                            <p class="description">Вес: </p>
                                            <p class="hint">{{ $product->{'weight'} }}</p>
                                        @endif
                                    </div>
                                    <div class="wrapper">
                                        <p class="price">{{ $product->{'price'} }} ₽</p>
                                        <div class="buttons_area">
                                            <button class="add_button" id="add{{ $product->{'id'} }}" onclick="registerChildButtons({{ $product->{'id'} }})">Добавить</button>
                                            <div class="child_buttons" id="child{{ $product->{'id'} }}">
                                                <button class="button_child" id="child_minus{{ $product->{'id'} }}" onclick="childButtonClick({{ $product->{'id'} }}, false)">-</button>
                                                <p id="count{{ $product->{'id'} }}">1</p>
                                                <button class="button_child" id="child_plus{{ $product->{'id'} }}" onclick="childButtonClick({{ $product->{'id'} }}, true)">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div id="main_menu" class="secondary">
            <div class="category">
                <p class="category_name">Салаты</p>
                <div class="wrapper">
                    <div class="product">
                        <div>
                            <p class="product_name">Цезарь с курицей</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Курица, томат, лист салата, сухарики, пармезан, соус цезарь</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">200гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">330₽</p>
                            <div class="buttons_area">
                                <button class="add_button" id="add59" onclick="registerChildButtons(59)">Добавить</button>
                                <div class="child_buttons" id="child59">
                                    <button class="button_child" id="child_minus59" onclick="childButtonClick(59, false)">-</button>
                                    <p id="count59">1</p>
                                    <button class="button_child" id="child_plus59" onclick="childButtonClick(59, true)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Цезарь с креветками</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Креветки, томат, лист салата, сухарики, пармезан, соус цезарь</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">200гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">410₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Цезарь с семгой</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Слабо-соленая сёмга, томат, лист салата, сухарики, пармезан, соус цезарь</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">200гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">390₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Пикантный</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Копченый цыпленок, огурцы свежие, огурцы соленые, зелень, перец болгарский, орех, горчичный соус</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">250гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">310₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Деревенский</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Картофель, колбаски, лист салата, томат, чесночная заправка, гренки</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">250гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">230₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Греческий</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Лист салата, томат, огурцы, перец, оливки, сыр фета, красный лук, соус</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">230гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">310₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">С семгой и яйцом пашот</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Слабо-соленая сёмга, руккола, творожный сыр, огурцы, томат, яйцо, горчичный соус</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">230гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">420₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="category">
                <p class="category_name">Первые блюда</p>
                <div class="wrapper">
                    <div class="product">
                        <div>
                            <p class="product_name">Солянка</p>
                            <p class="description">Состав: </p>
                            <p class="hint">курица, помидоры</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">150гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">150₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Борщ с говядиной</p>
                            <p class="description">Вес: </p>
                            <p class="hint">340гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">330₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Почти том-ям</p>
                            <p class="description">Вес: </p>
                            <p class="hint">450гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">450₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Бульон с яйцом и сухариками</p>
                            <p class="description">Вес: </p>
                            <p class="hint">280гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">240₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Грибной крем-суп</p>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">320₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="category">
                <p class="category_name">Паста</p>
                <div class="wrapper">
                    <div class="product">
                        <div>
                            <p class="product_name">Карбонара</p>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">330₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">С креветками и сёмгой</p>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">390₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">С курицей и соусом песто</p>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">360₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">С копченой курицей и грибами</p>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">350₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="category">
                <p class="category_name">Горячие блюда</p>
                <div class="wrapper">
                    <div class="product">
                        <div>
                            <p class="product_name">Жареха</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Картошка, помидор, чеснок, зелень, бекон, курица, колбаски</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">390₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Жареха по-охотничьи</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Картошка, колбаски, яйцо, зелень, чеснок</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">290₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Запеканка</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Курица, картошка, помидоры, сыр, грибы</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">300гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">320₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Колбаски</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Свинина или курица</p><br>
                            <p class="description">Вес: </p>
                            <p class="hint">2шт.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">320₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Куриная грудка с моцареллой и соусом песто</p>
                            <p class="description">Вес: </p>
                            <p class="hint">200гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">340₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Куриное филе с арахисом, семечками и сладким перцем</p>
                            <p class="description">Вес: </p>
                            <p class="hint">200гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">340₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Отбивная из свинины в яблочно-сливочном соусе</p>
                            <p class="description">Вес: </p>
                            <p class="hint">240гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">380₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Бифштекс</p>
                            <p class="description">Вес: </p>
                            <p class="hint">240гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">390₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Стейк сёмги в сырном соусе</p>
                            <p class="description">Вес: </p>
                            <p class="hint">180гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">580₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Стейк сёмги на гриле с соусом тар-тар</p>
                            <p class="description">Вес: </p>
                            <p class="hint">150гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">530₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="category">
                <p class="category_name">Гарниры</p>
                <div class="wrapper">
                    <div class="product">
                        <div>
                            <p class="product_name">Картофель запеченый</p>
                            <p class="description">Вес: </p>
                            <p class="hint">150гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">130₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Рис</p>
                            <p class="description">Вес: </p>
                            <p class="hint">150гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">100₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Спагетти</p>
                            <p class="description">Вес: </p>
                            <p class="hint">150гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">110₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Гречка с грибами в сливках</p>
                            <p class="description">Вес: </p>
                            <p class="hint">150гр.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">110₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="category">
                <p class="category_name">Салаты</p>
                <div class="wrapper">
                    <div class="product">
                        <div>
                            <p class="product_name">Сэндвич с ветчиной и сыром</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Ветчина, сыр, свежий огурец, чесночный соус</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">160₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Сэндвич с сёмгой</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Сёмга, огурец, творожный сыр</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">280₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Тортилья с ветчиной и сыром</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Ветчина, бекон, сыр, огурец, зелень, помидор, яйцо, чесночный соус</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">190₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Тортилья с лососем</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Сёмга, огурец, творожный сыр, яйцо, зелень</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">230₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Тортилья с курицей</p>
                            <p class="description">Состав: </p>
                            <p class="hint">Курица, сыр, зелень, яйцо, соус песто, помидоры</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">230₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Блинчики</p>
                            <p class="description">Вес: </p>
                            <p class="hint">3шт.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">70₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                    <div class="product">
                        <div>
                            <p class="product_name">Сырники</p>
                            <p class="description">Вес: </p>
                            <p class="hint">2шт.</p>
                        </div>
                        <div class="wrapper">
                            <p class="price">90₽</p>
                            <button class="add_button">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="order_page">
            <div id="order_content" style="padding-left: 10px; padding-right: 10px">
                <div class="wrapper" id="order_list">
                </div>
            </div>
            <div class="cost_area">
                <div style="text-align: left">
                    <p>Итого:</p>
                </div>
                <div style="text-align: right">
                    <p id="total_cost">0 ₽</p>
                </div>
            </div>
        </div>
    </div>
    <script>

        function childButtonClick(id, action) {
            if(action) {
                order[id] += 1;
            } else {
                order[id] -= 1;
                if(order[id] === 0) {
                    document.getElementById('add' + id).style.display = 'block';
                    document.getElementById('child' + id).style.display = 'none';
                    delete order[id];
                }
            }
            document.getElementById('count' + id).innerText = order[id];
        }

        function registerChildButtons(id) {
            document.getElementById('add' + id).style.display = 'none';
            document.getElementById('child' + id).style.display = 'grid';
            order[id] = 1;
            document.getElementById('count' + id).innerText = order[id];
        }

        function formatPrice(price) {
            if(parseInt((price/1000)) > 0) {
                if(price % 1000 < 100) {
                    return parseInt((price/1000)) + ' 0' + (price % 1000);
                } else {
                    return parseInt((price/1000)) + ' ' + (price % 1000);
                }
            } else {
                return price;
            }
        }

        function emptyOrderPage() {
            document.getElementById('order_list').innerText = '';
            document.getElementById('order_list').appendChild(document.createElement("br"));
            let empty = document.createElement("p");
            empty.innerText = 'Корзина пуста';
            empty.style.color = tg.themeParams.hint_color;
            document.getElementById('order_list').appendChild(empty);
            document.getElementById('order_list').appendChild(document.createElement("br"));
            document.getElementById('total_cost').innerText= '0 ₽';
        }

        function setOrderPage() {
            let orderList = document.getElementById('order_list');
            orderList.innerText = '';
            let keys = Object.keys(order);

            if(keys.length > 0) {
                let total_cost = 0;
                keys.forEach(i => {
                    let item = document.createElement('div');
                    item.className = 'order_item';
                    let name = document.createElement('p');
                    name.className = 'product_name';
                    name.innerText = menu[i]['name'];
                    let count = document.createElement('p');
                    count.innerText = 'x' + order[i];
                    count.style.color = tg.themeParams.hint_color;
                    let cost = document.createElement('p');
                    cost.style.textAlign = 'right';
                    cost.innerText = formatPrice(menu[i]['price']) + ' ₽';
                    total_cost += menu[i]['price'] * order[i];
                    item.appendChild(name);
                    item.appendChild(count);
                    item.appendChild(cost);
                    orderList.appendChild(item);
                });
                document.getElementById('total_cost').innerText = formatPrice(total_cost) + ' ₽';
            } else {
                emptyOrderPage();
            }
        }

        function sendData() {
            let data = [];
            Object.keys(order).forEach(i => {
                data.push({
                    "id" : i,
                    "count" : order[i]
                });
            });

            if(data.length !== 0) {
                tg.sendData(JSON.stringify(data));
            } else {
                tg.MainButton.setText('Ваша корзина пуста');
            }
        }

        let tg = window.Telegram.WebApp;
        tg.expand();

        let newHeight = document.documentElement.clientHeight - 130;
        document.getElementById('main').style.height = newHeight + 'px';

        let order = {};
        let menu = {};
        tg.MainButton.setText('Оформить заказ');
        tg.MainButton.onClick(sendData);

        @if(!is_null($menu))
            let temp = @json($menu);
            Object.keys(temp).forEach(i => {
                temp[i].forEach(j => {
                    menu[j['id']] = j
                })
            });
        @endif

        @if(!is_null($order))

        @endif


        let lunchButton = document.getElementById('lunch_menu_button');
        let mainButton = document.getElementById('main_menu_button');

        lunchButton.addEventListener('click', () => {
            document.getElementById('main').style.display = 'none';
            document.getElementById('page').innerText = 'Бизнес-ланч';
            document.getElementById('lunch_menu').style.display = 'block';
            document.getElementById('back_button').style.display = 'block';
        });

        mainButton.addEventListener('click', () => {
            document.getElementById('main').style.display = 'none';
            document.getElementById('page').innerText = 'Основное меню';
            document.getElementById('main_menu').style.display = 'block';
            document.getElementById('back_button').style.display = 'block';
        });

        let returnB = document.getElementById('back_button');
        returnB.addEventListener('click', () => {
            document.getElementById('lunch_menu').style.display = 'none';
            document.getElementById('main_menu').style.display = 'none';
            document.getElementById('order_page').style.display = 'none';
            document.getElementById('page').innerText = 'Главная';
            document.getElementById('main').style.display = 'block';
            document.getElementById('back_button').style.display = 'none';
            tg.MainButton.hide();
            emptyOrderPage();
        });

        document.getElementById('order_button').addEventListener('click', () => {
            document.getElementById('lunch_menu').style.display = 'none';
            document.getElementById('main_menu').style.display = 'none';
            document.getElementById('main').style.display = 'none';
            document.getElementById('page').innerText = 'Заказ';
            document.getElementById('order_page').style.display = 'block';
            document.getElementById('back_button').style.display = 'block';
            setOrderPage();
            tg.MainButton.setText('Оформить заказ');
            tg.MainButton.show();
        });
    </script>
</body>
</html>
