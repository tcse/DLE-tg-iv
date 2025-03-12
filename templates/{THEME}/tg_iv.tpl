<!DOCTYPE html>
<!-- tg_iv.tpl -->
<head>
    <title>{title}</title>
    <meta property="og:site_name" content="{og-site-name}">
    <meta property="og:description" content="{og-description}">
    <meta property="article:author" content="{autor}">
    <meta property="og:image" content="{image-1}">
    <meta property="telegram:channel" content="{tg-chanel}">
    <meta property="tg:site_verification" content="g7j8/rPFXfhyrq5q0QQV7EsYWv4=">
    <meta property="article:published_time" content="{date}">
    [desktop]
    <!-- стили для настольных систем -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Основные настройки */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 0;
            max-width: 720px;
            margin: 0 auto;
        }

        /* Заголовки */
        h1, h2, h3, h4, h5, h6 {
            font-weight: bold;
            margin: 1em 0 0.5em;
            line-height: 1.2;
        }

        h1 { font-size: 2em; }
        h2 { font-size: 1.8em; }
        h3 { font-size: 1.6em; }
        h4 { font-size: 1.4em; }
        h5 { font-size: 1.2em; }
        h6 { font-size: 1em; }

        /* Параграфы */
        p {
            margin: 1em 0;
        }

        /* Ссылки */
        a {
            color: #0084B4;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Изображения */
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 1em auto;
        }

        /* Блоки цитат */
        blockquote {
            border-left: 4px solid #ccc;
            padding: 0 1em;
            margin: 1em 0;
            color: #666;
            font-style: italic;
        }

        /* Списки */
        ul, ol {
            margin: 1em 0;
            padding-left: 1.5em;
        }

        li {
            margin: 0.5em 0;
        }

        /* Код и предварительно форматированный текст */
        pre {
            background-color: #f4f4f4;
            padding: 1em;
            overflow-x: auto;
            border-radius: 4px;
        }

        code {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f4f4f4;
            padding: 2px 4px;
            border-radius: 4px;
        }

        /* Таблицы */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1em 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0.5em;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        /* Горизонтальные линии */
        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 2em 0;
        }

        /* Футер */
        footer {
            margin-top: 2em;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }

        /* Медиа запросы для адаптивности */
        @media (max-width: 600px) {
            body {
                padding: 0 1em;
            }
            
            h1 { font-size: 1.8em; }
            h2 { font-size: 1.6em; }
            h3 { font-size: 1.4em; }
        }
    </style>
    [/desktop]
</head>

<body>
    <div class="article">
        <article class="article__content">
            <!-- your "IV-compliant" article body goes here -->
            <!-- for general idea of what "IV-compliant" HTML is refer to IV documentation: 
                https://instantview.telegram.org/docs#supported-types (HTML counterpart and allowed children columns) -->

            <!-- если первый элемент в статье - это рисунок, он будет установлен/использован как обложка статьи -->
            <figure>
                <img src="{tg-cover-url}" />
                <figcaption>{category-name}</figcaption>
            </figure>

            {full-story}

            <h3>Источник: {autor}</h3>
            <p><a href="{full-link}">Перейти на сайт</a></p>
            <p>
                Другие материалы на сайте <a href="{home-url}">{og-site-name}</a>
            </p>

            {* <h4>Теги используемые в шаблоне tg-iv.tpl</h4>
            <p>
                <b>{ category-name }</b> - {category-name}<br>
                <b>{ autor }</b> - {autor}<br>
                <b>{ alt-name }</b> - {alt-name}<br>
                <b>{ date }</b> - {date}<br>
                <b>{ category-alt-name }</b> - {category-alt-name}<br>
                <b>{ og-site-name }</b> - {og-site-name}<br>
                <b>{ og-description }</b> - {og-description}<br>
                <b>{ home-url }</b> - {home-url}<br>
                <b>{ tg-chanel }</b> - {tg-chanel}<br>
                <b>{ tg-cover-url }</b> - {tg-cover-url}<br>
            </p> *}
            
        </article>
    </div>
</body>

</html>