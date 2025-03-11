<?php
/*
=====================================================
 Telegram Instant View Plugin for DLE 18
-----------------------------------------------------
 https://tcse-cms.com/
-----------------------------------------------------
 File: engine/modules/tg_instant_view_rss.php
-----------------------------------------------------
 Use: the telegram instant_view news feeds
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
    header( "HTTP/1.1 403 Forbidden" );
    header ( 'Location: ../' );
    die( "Hacking attempt!" );
}

if($dle_module != "main" AND $dle_module != "allnews" AND $dle_module != "catalog" AND $dle_module != "cat") {
    header( "HTTP/1.1 403 Forbidden" );
    header ( 'Location: /' );
    die("Hacking attempt!");
}

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php'));
include_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $config['langs'] . '/website.lng'));

if (strpos($config['http_home_url'], "//") === 0) $config['http_home_url'] = "https:".$config['http_home_url'];
elseif (strpos($config['http_home_url'], "/") === 0) $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

$tpl = new dle_template( );
$tpl->dir = ROOT_DIR . '/templates';
define( 'TEMPLATE_DIR', $tpl->dir );

$member_id['user_group'] = 5;

if( $category != '' ) $category_id = get_ID( $cat_info, $category );
else $category_id = false;

$view_template = "rss";

$config['allow_cache'] = true;
$config['allow_banner'] = true; // Вывод банеров разрешен [banner_X] {banner_X} [/banner_X]
$config['rss_number'] = intval( $config['tg_instant_view_rss_number'] ); // Количество экспортируемых новостей (из настроек RSS движка)
$config['rss_format'] = 1; // 0 - Простой - выводит только текст новости без форматирования. 1 - Полный - выводит весь текст новости с сохранением форматирования и картинок.
$cstart = 0;

if ( $user ) $config['allow_cache'] = false;

if( $_GET['subaction'] == 'allnews' ) $config['home_title'] = $lang['show_user_news'] . ' ' . htmlspecialchars( $user, ENT_QUOTES, $config['charset'] ) . " - " . $config['home_title'];
elseif( $_GET['do'] == 'cat' ) $config['home_title'] = stripslashes( $cat_info[$category_id]['name'] ) . " - " . $config['home_title'];

$rss_content = <<<XML
<?xml version="1.0" encoding="{$config['charset']}"?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>{$config['home_title']}</title>
<link>{$config['http_home_url']}</link>
<language>{$lang['wysiwyg_language']}</language>
<description>{$config['home_title']}</description>
<generator>DataLife Engine</generator>
XML;

if( !file_exists( $tpl->dir . "/rss_tgiv.tpl" ) ) {

    $tpl->template = <<<HTML
<item>
<title>{title}</title>
<guid isPermaLink="true">{rsslink}</guid>
<link>{rsslink}</link>
<description><![CDATA[{short-story}]]></description>
<content:encoded><![CDATA[{full-story}]]></content:encoded>
<category><![CDATA[{category}]]></category>
<dc:creator>{rssauthor}</dc:creator>
<pubDate>{rssdate}</pubDate>
</item>
HTML;

    $tpl->copy_template = $tpl->template;

} else {
    
    $tpl->load_template( 'rss_tgiv.tpl' );
    
}


if( $config['site_offline'] OR ! $config['allow_rss'] ) {
    
    $rss_content .= <<<XML
<item>
<title>RSS in offline mode</title>
<guid isPermaLink="true"></guid>
<link></link>
<description>RSS in offline mode</description>
<category>undefined</category>
<dc:creator>DataLife Engine</dc:creator>
<pubDate>DataLife Engine</pubDate>
</item>
XML;

} else {
    
    if( $config['rss_format'] == 1 ) {
        
        $tpl->template = str_replace( '[fullrss]', '', $tpl->template );
        $tpl->template = str_replace( '[/fullrss]', '', $tpl->template );
        $tpl->template = preg_replace( "'\\[yandexrss\\](.*?)\\[/yandexrss\\]'si", "", $tpl->template );
        $tpl->template = preg_replace( "'\\[shortrss\\](.*?)\\[/shortrss\\]'si", "", $tpl->template );
        $tpl->template = trim($tpl->template);
        
    } elseif( $config['rss_format'] == 2 ) {
        
        $rss_content = <<<XML
<?xml version="1.0" encoding="{$config['charset']}"?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>{$config['home_title']}</title>
<link>{$config['http_home_url']}</link>
<language>{$lang['wysiwyg_language']}</language>
<description>{$config['home_title']}</description>
<generator>DataLife Engine</generator>
XML;
        
        $tpl->template = preg_replace( "'\\[fullrss\\](.*?)\\[/fullrss\\]'si", "", $tpl->template );
        $tpl->template = trim($tpl->template);      
    } else {
        

        $tpl->template = preg_replace( "'\\[fullrss\\](.*?)\\[/fullrss\\]'si", "", $tpl->template );
        $tpl->template = trim($tpl->template);  
    }
    
    $tpl->copy_template = $tpl->template;
    
    include_once (DLEPlugins::Check(ENGINE_DIR . '/engine.php'));
    
    $rss_content .= $tpl->result['content'];
}

$rss_content .= '</channel></rss>';

$rss_content = str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $rss_content );

header( "Content-type: application/xml; charset=".$config['charset'] );
echo $rss_content;

die();

?>