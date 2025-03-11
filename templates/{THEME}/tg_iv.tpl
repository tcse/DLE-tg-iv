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