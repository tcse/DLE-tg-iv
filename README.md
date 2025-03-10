# DLE-tg-iv
Telegram Instant View by TCSE for DataLife Engine

В шаблон полной новости fullstory.tpl добавить в нужное место ссылку на Telegram Instant View
Например
		<pre><code>&lt;a href="/tg-iv,{news-id}-.html" target="_blank">Telegram Instant View&lt;/a></code></pre>

В файле шаблона Telegram Instant View в папке {THEME}/tg_iv.tpl обязательно оставить метатег
<code>
&lt;meta property="tg:site_verification" content="g7j8/rPFXfhyrq5q0QQV7EsYWv4=">
</code>
так как именно он позволяет без каких либо проверок на стороне Telegram делать вашим страницам IV версию.

Установка количества новостей экспортируемых в Telegram Instant View задается в разделе [b]Настройки скрипта[/b] -> [b]Настройки системы[/b] вкладка RSS вписать необходимое количество.

Для получения красивого адреса rss ленты для экспорта в Telegram необходимо добавить правила редиректов.

В /.htaccess после строки

<code>RewriteRule ^rss.xml$ index.php?mod=rss [L]</code>

вставить

<code>RewriteRule ^rss_tgiv.xml$ index.php?mod=tg_instant_view_rss [L]</code>
  
Всё. Теперь у нас есть две ленты RSS на сайте:

site.ru/rss.xml - Стандартная лента движка;
site.ru/rss_tgiv.xml - RSS лента в формате telegram instant view полным текстом новостей.
