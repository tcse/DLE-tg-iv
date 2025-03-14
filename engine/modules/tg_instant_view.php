<?php
/*
=====================================================
 Плагин Telegram Instant View для DLE 18
-----------------------------------------------------
 https://tcse-cms.com/
=====================================================
 Файл: engine/modules/tg_instant_view.php
-----------------------------------------------------
 Использование: отображение полной статьи в Telegram Instant View
=====================================================
*/
if (!defined('DATALIFEENGINE')) {
    header("HTTP/1.1 403 Forbidden");
    header('Location: ../../');
    die("Попытка взлома!");
}
// Обработка URL Telegram IV, если это необходимо
$current_url = $_SERVER['REQUEST_URI'];
if (strpos($current_url, 'tg-iv,') !== false) {
    // Извлечение ID новости из URL
    preg_match('/tg-iv,(\d+)/', $current_url, $matches);
    if (isset($matches[1])) {
        $news_id = intval($matches[1]);
        if ($news_id) {
            // Загрузка содержимого новости с информацией об авторе и дополнительными полями
            $row = $db->super_query("SELECT p.id, p.title, p.autor, p.date, p.full_story, p.short_story, p.category, p.alt_name, p.descr, p.xfields, p.tags 
                                    FROM " . PREFIX . "_post p 
                                    WHERE id = '{$news_id}'");
            if ($row['id']) {
                // Получение информации о категории
                $category_info = array();
                $category_name = "Без категории"; // Значение по умолчанию
                if (!empty($row['category'])) {
                    // В DLE поле категории может содержать несколько ID категорий, разделенных запятыми
                    // Мы получим первую категорию для простоты
                    $category_ids = explode(',', $row['category']);
                    $first_category_id = intval($category_ids[0]);
                    // Запрос к таблице категорий для получения имени категории
                    $cat_row = $db->super_query("SELECT id, name, alt_name 
                                                FROM " . PREFIX . "_category 
                                                WHERE id = '{$first_category_id}'");
                    if ($cat_row['id']) {
                        $category_name = $cat_row['name'];
                        $category_info = $cat_row;
                    }
                }
                // Загрузка шаблона {THEME}/tg_iv.tpl
                $tpl->load_template('tg_iv.tpl');
                // Обработка переменных шаблона для статьи
                $tpl->set('{title}', $row['title']);
                $tpl->set('{autor}', $row['autor']);
                $tpl->set('{alt-name}', $row['alt_name']);
                $tpl->set('{date}', date('c', strtotime($row['date'])));
                $tpl->set('{full-story}', $row['full_story']);
                $tpl->set('{short-story}', $row['short_story']);
                $tpl->set('{home-url}', $config['http_home_url']);
                // Обработка тегов
                if (!empty($row['tags'])) {
                    $tpl->set('{tags}', $row['tags']);
                } else {
                    $tpl->set('{tags}', '');
                }
                // Добавление информации о категории в шаблон
                $tpl->set('{category-name}', $category_name);
                // Если нужно добавить больше полей категории, можно сделать это так:
                if (isset($category_info['alt_name'])) {
                    $tpl->set('{category-alt-name}', $category_info['alt_name']);
                } else {
                    $tpl->set('{category-alt-name}', '');
                }
                // Создание полной ссылки на оригинальную статью
                $full_link = $config['http_home_url'];
                if ($row['category']) {
                    $full_link .= $row['category'] . '/';
                }
                $full_link .= $row['id'] . '-' . $row['alt_name'] . '.html';
                $tpl->set('{full-link}', $full_link);
                // Обработка изображений с условными тегами
                $images = array();
                // Извлечение изображений из полной статьи
                preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $row['full_story'], $img_matches);
                if (!empty($img_matches[1])) {
                    $images = $img_matches[1];
                }
                // Обработка тегов изображений с условными парами
                for ($i = 1; $i <= 10; $i++) {
                    if (isset($images[$i-1])) {
                        $tpl->set('{image-' . $i . '}', $images[$i-1]);
                        $tpl->set_block("'\\[image-{$i}\\](.*?)\\[/image-{$i}\\]'si", "\\1");
                        $tpl->set_block("'\\[not-image-{$i}\\](.*?)\\[/not-image-{$i}\\]'si", "");
                    } else {
                        $tpl->set('{image-' . $i . '}', $config['http_home_url'] . 'templates/' . $config['skin'] . '/dleimages/no_image.jpg');
                        $tpl->set_block("'\\[image-{$i}\\](.*?)\\[/image-{$i}\\]'si", "");
                        $tpl->set_block("'\\[not-image-{$i}\\](.*?)\\[/not-image-{$i}\\]'si", "\\1");
                    }
                }
                // Обработка тега Telegram канала с условными парами
                $tg_channel = '@tcsecms'; // Можно сделать это настраиваемым
                if (!empty($tg_channel)) {
                    $tpl->set('{tg-chanel}', $config['tg_instant_view_chanel']);
                    $tpl->set_block("'\\[tg-chanel\\](.*?)\\[/tg-chanel\\]'si", "\\1");
                    $tpl->set_block("'\\[not-tg-chanel\\](.*?)\\[/not-tg-chanel\\]'si", "");
                } else {
                    $tpl->set('{tg-chanel}', '');
                    $tpl->set_block("'\\[tg-chanel\\](.*?)\\[/tg-chanel\\]'si", "");
                    $tpl->set_block("'\\[not-tg-chanel\\](.*?)\\[/not-tg-chanel\\]'si", "\\1");
                }
                // Обработка тега URL обложки Telegram с условными парами
                $tg_cover_url = '/engine/skins/images/placeholder.svg'; // Можно установить это как обложку по умолчанию или сделать настраиваемым
                if (!empty($tg_cover_url)) {
                    $tpl->set('{tg-cover-url}', $config['tg_instant_view_cover']);
                    $tpl->set_block("'\\[tg-cover-url\\](.*?)\\[/tg-cover-url\\]'si", "\\1");
                    $tpl->set_block("'\\[not-tg-cover-url\\](.*?)\\[/not-tg-cover-url\\]'si", "");
                } else {
                    $tpl->set('{tg-cover-url}', '');
                    $tpl->set_block("'\\[tg-cover-url\\](.*?)\\[/tg-cover-url\\]'si", "");
                    $tpl->set_block("'\\[not-tg-cover-url\\](.*?)\\[/not-tg-cover-url\\]'si", "\\1");
                }
                // Добавление мета-тегов Open Graph с использованием конфигурации DLE
                // Используем название сайта из конфигурации
                $tpl->set('{og-site-name}', $config['home_title']);
                // Для og:description используем описание статьи, если оно доступно, иначе используем краткое описание или описание сайта
                if (!empty($row['descr'])) {
                    $og_description = $row['descr'];
                } elseif (!empty($row['short_story'])) {
                    // Удаляем HTML-теги и ограничиваем до 200 символов
                    $og_description = substr(strip_tags($row['short_story']), 0, 200) . '...';
                } else {
                    // Используем описание сайта из конфигурации
                    $og_description = $config['description'];
                }
                $tpl->set('{og-description}', $og_description);
                // Для article:author у нас уже есть {autor}, но добавим специальный OG-тег
                $tpl->set('{og-author}', $row['autor']);
                // Обработка дополнительных полей - аналогично тому, как это делается в show.custom.php
                if (!empty($row['xfields'])) {
                    $xfields = xfieldsload();
                    $row['xfields'] = stripslashes($row['xfields']);
                    $xfieldsdata = xfieldsdataload($row['xfields']);
                    foreach ($xfields as $value) {
                        $preg_safe_name = preg_quote($value[0], "'");
                        if (empty($xfieldsdata[$value[0]])) {
                            $tpl->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template);
                            $tpl->copy_template = str_replace("[xfnotgiven_{$value[0]}]", "", $tpl->copy_template);
                            $tpl->copy_template = str_replace("[/xfnotgiven_{$value[0]}]", "", $tpl->copy_template);
                        } else {
                            $tpl->copy_template = preg_replace("'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template);
                            $tpl->copy_template = str_replace("[xfgiven_{$value[0]}]", "", $tpl->copy_template);
                            $tpl->copy_template = str_replace("[/xfgiven_{$value[0]}]", "", $tpl->copy_template);
                        }
                        $tpl->set("[xfvalue_{$value[0]}]", $xfieldsdata[$value[0]]);
                    }
                } else {
                    // Если нет дополнительных полей, удаляем все связанные теги
                    $tpl->copy_template = preg_replace("'\\[xfgiven_(.*?)\\](.*?)\\[/xfgiven_(.*?)\\]'is", "", $tpl->copy_template);
                    $tpl->copy_template = preg_replace("'\\[xfnotgiven_(.*?)\\](.*?)\\[/xfnotgiven_(.*?)\\]'is", "", $tpl->copy_template);
                    $tpl->copy_template = preg_replace("'\\[xfvalue_(.*?)\\]'i", "", $tpl->copy_template);
                }
                // Обработка баннерных тегов
                if (function_exists('banners')) {
                    $tpl->copy_template = banners($tpl->copy_template);
                }
                // Обработка других тегов fullstory.tpl
                $tpl->set('{views}', $row['news_read']);
                $tpl->set('{comments-num}', $row['comm_num']);
                // Компиляция шаблона
                $tpl->compile('content');
                // Вывод содержимого
                echo $tpl->result['content'];
                die();
            }
        }
    }
}

// Вспомогательная функция для загрузки конфигурации дополнительных полей
if (!function_exists('xfieldsload')) {
    function xfieldsload() {
        global $config;
        $path = ENGINE_DIR . '/data/xfields.txt';
        if (!file_exists($path)) {
            return array();
        }
        $filecontents = file_get_contents($path);
        if (!$filecontents) {
            return array();
        }
        $fields = explode("\r
", $filecontents);
        $xfields = array();
        foreach ($fields as $field) {
            if (trim($field) != '') {
                $xfield = explode("|", $field);
                $xfields[] = $xfield;
            }
        }
        return $xfields;
    }
}
// Вспомогательная функция для загрузки данных дополнительных полей
if (!function_exists('xfieldsdataload')) {
    function xfieldsdataload($xfieldsdata) {
        if (!$xfieldsdata) {
            return array();
        }
        $data = array();
        $xfieldsdata = explode("||", $xfieldsdata);
        foreach ($xfieldsdata as $xfielddata) {
            list($xfielddataname, $xfielddatavalue) = explode("|", $xfielddata);
            $data[$xfielddataname] = $xfielddatavalue;
        }
        return $data;
    }
}
?>
