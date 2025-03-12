<?php
/*
=====================================================
 Telegram Instant View Plugin for DLE 18
-----------------------------------------------------
 https://tcse-cms.com/
=====================================================
 File: engine/modules/tg_instant_view.php
-----------------------------------------------------
 Use: the telegram instant_view fullstory
=====================================================
*/

if (!defined('DATALIFEENGINE')) {
    header("HTTP/1.1 403 Forbidden");
    header('Location: ../../');
    die("Hacking attempt!");
}

// Process the Telegram IV URL if needed
$current_url = $_SERVER['REQUEST_URI'];

if (strpos($current_url, 'tg-iv,') !== false) {
    // Extract the news ID from the URL
    preg_match('/tg-iv,(\d+)/', $current_url, $matches);
    
    if (isset($matches[1])) {
        $news_id = intval($matches[1]);
        
        if ($news_id) {
            // Load the news content with author information and xfields
            $row = $db->super_query("SELECT p.id, p.title, p.autor, p.date, p.full_story, p.short_story, p.category, p.alt_name, p.descr, p.xfields, p.tags 
                                    FROM " . PREFIX . "_post p 
                                    WHERE id = '{$news_id}'");
            
            if ($row['id']) {
                // Get category information
                $category_info = array();
                $category_name = "Uncategorized"; // Default value
                
                if (!empty($row['category'])) {
                    // In DLE, the category field can contain multiple category IDs separated by commas
                    // We'll get the first category for simplicity
                    $category_ids = explode(',', $row['category']);
                    $first_category_id = intval($category_ids[0]);
                    
                    // Query the category table to get the category name
                    $cat_row = $db->super_query("SELECT id, name, alt_name 
                                                FROM " . PREFIX . "_category 
                                                WHERE id = '{$first_category_id}'");
                    
                    if ($cat_row['id']) {
                        $category_name = $cat_row['name'];
                        $category_info = $cat_row;
                    }
                }
                
                // Load the {THEME}/tg_iv.tpl template
                $tpl->load_template('tg_iv.tpl');
                
                // Process template variables for the article
                $tpl->set('{title}', $row['title']);
                $tpl->set('{autor}', $row['autor']);
                $tpl->set('{alt_name}', $row['alt_name']);
                $tpl->set('{date}', date('c', strtotime($row['date'])));
                $tpl->set('{full-story}', $row['full_story']);
                $tpl->set('{short-story}', $row['short_story']);
                $tpl->set('{home-url}', $config['http_home_url']);
                
                // Process tags
                if (!empty($row['tags'])) {
                    $tpl->set('{tags}', $row['tags']);
                } else {
                    $tpl->set('{tags}', '');
                }
                
                // Add category information to template
                $tpl->set('{category-name}', $category_name);
                
                // If you want to add more category fields, you can do it like this:
                if (isset($category_info['alt_name'])) {
                    $tpl->set('{category-alt-name}', $category_info['alt_name']);
                } else {
                    $tpl->set('{category-alt-name}', '');
                }
                
                // Create the full link back to the original article
                $full_link = $config['http_home_url'];
                if ($row['category']) {
                    $full_link .= $row['category'] . '/';
                }
                $full_link .= $row['id'] . '-' . $row['alt_name'] . '.html';
                
                $tpl->set('{full-link}', $full_link);
                
                // Process images with conditional tags
                $images = array();
                
                // Extract images from the full story
                preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $row['full_story'], $img_matches);
                
                if (!empty($img_matches[1])) {
                    $images = $img_matches[1];
                }
                
                // Process image tags with conditional pairs
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
                
                // Process Telegram channel tag with conditional pairs
                $tg_channel = '@tcsecms'; // You can make this configurable
                if (!empty($tg_channel)) {
                    $tpl->set('{tg-chanel}', $config['tg_instant_view_chanel']);
                    $tpl->set_block("'\\[tg-chanel\\](.*?)\\[/tg-chanel\\]'si", "\\1");
                    $tpl->set_block("'\\[not-tg-chanel\\](.*?)\\[/not-tg-chanel\\]'si", "");
                } else {
                    $tpl->set('{tg-chanel}', '');
                    $tpl->set_block("'\\[tg-chanel\\](.*?)\\[/tg-chanel\\]'si", "");
                    $tpl->set_block("'\\[not-tg-chanel\\](.*?)\\[/not-tg-chanel\\]'si", "\\1");
                }
                
                // Process Telegram cover URL tag with conditional pairs
                $tg_cover_url = '/engine/skins/images/placeholder.svg'; // You can set this to a default cover image or make it configurable
                if (!empty($tg_cover_url)) {
                    $tpl->set('{tg-cover-url}', $config['tg_instant_view_cover']);
                    $tpl->set_block("'\\[tg-cover-url\\](.*?)\\[/tg-cover-url\\]'si", "\\1");
                    $tpl->set_block("'\\[not-tg-cover-url\\](.*?)\\[/not-tg-cover-url\\]'si", "");
                } else {
                    $tpl->set('{tg-cover-url}', '');
                    $tpl->set_block("'\\[tg-cover-url\\](.*?)\\[/tg-cover-url\\]'si", "");
                    $tpl->set_block("'\\[not-tg-cover-url\\](.*?)\\[/not-tg-cover-url\\]'si", "\\1");
                }
                
                // Add Open Graph meta tag variables using DLE configuration
                // Use the site's title from config
                $tpl->set('{og-site-name}', $config['home_title']);
                
                // For og:description, use article description if available, otherwise use short story or site description
                if (!empty($row['descr'])) {
                    $og_description = $row['descr'];
                } elseif (!empty($row['short_story'])) {
                    // Strip HTML tags and limit to 200 characters
                    $og_description = substr(strip_tags($row['short_story']), 0, 200) . '...';
                } else {
                    // Use the site's description from config
                    $og_description = $config['description'];
                }
                
                $tpl->set('{og-description}', $og_description);
                
                // For article:author, we already have {autor} but let's add a specific OG tag
                $tpl->set('{og-author}', $row['autor']);
                
                // Process xfields - similar to how it's done in show.custom.php
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
                    // If no xfields, remove all xfield related tags
                    $tpl->copy_template = preg_replace("'\\[xfgiven_(.*?)\\](.*?)\\[/xfgiven_(.*?)\\]'is", "", $tpl->copy_template);
                    $tpl->copy_template = preg_replace("'\\[xfnotgiven_(.*?)\\](.*?)\\[/xfnotgiven_(.*?)\\]'is", "", $tpl->copy_template);
                    $tpl->copy_template = preg_replace("'\\[xfvalue_(.*?)\\]'i", "", $tpl->copy_template);
                }
                
                // Process banner tags
                if (function_exists('banners')) {
                    $tpl->copy_template = banners($tpl->copy_template);
                }
                
                // Process other fullstory.tpl tags
                $tpl->set('{views}', $row['news_read']);
                $tpl->set('{comments-num}', $row['comm_num']);
                
                // Compile the template
                $tpl->compile('content');
                
                // Output the content
                echo $tpl->result['content'];
                die();
            }
        }
    }
}

// Add a custom tag for the Telegram IV link with alt_name
if ($dle_module == 'showfull') {
    // We're in a full story view, so we can add our custom tag
    
    // Function to generate the Telegram IV link with alt_name
    function generate_tg_iv_link_with_alt_name() {
        global $config, $row;
        
        // Make sure we have the news ID and alt_name
        if (!isset($row['id']) || !$row['id'] || !isset($row['alt_name']) || !$row['alt_name']) {
            return '#';
        }
        
        // Create the tg-iv URL with alt_name
        $tg_iv_url = 'tg-iv,' . $row['id'] . '-' . $row['alt_name'] . '.html';
        
        // Add category if available
        if (isset($row['category']) && $row['category']) {
            $tg_iv_url = $row['category'] . '/' . $tg_iv_url;
        }
        
        return $config['http_home_url'] . $tg_iv_url;
    }
    
    // Replace the tag in the template
    if (isset($tpl) && is_object($tpl) && isset($tpl->copy_template)) {
        $tpl->copy_template = str_replace('{tg-iv-link-with-alt}', generate_tg_iv_link_with_alt_name(), $tpl->copy_template);
    }
}

// Helper function to load xfields configuration
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
        
        $fields = explode("\r\n", $filecontents);
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

// Helper function to load xfields data
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