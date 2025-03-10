<?php
/*
=====================================================
 Telegram Instant View Plugin for DLE 18
-----------------------------------------------------
 https://tcse-cms.com/
-----------------------------------------------------
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
            // Load the news content with author information
            $row = $db->super_query("SELECT p.id, p.title, p.autor, p.date, p.full_story, p.short_story, p.category, p.alt_name, p.descr 
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
                
                // Load the tg_iv.tpl template
                $tpl->load_template('tg_iv.tpl');
                
                // Process template variables for the article
                $tpl->set('{title}', $row['title']);
                $tpl->set('{autor}', $row['autor']);
                $tpl->set('{alt-name}', $row['alt_name']);
                $tpl->set('{date}', date('c', strtotime($row['date'])));
                $tpl->set('{full-story}', $row['full_story']);
                $tpl->set('{home-url}', $config['http_home_url']);
                $tpl->set('{tg-chanel}', $config['tg_instant_view_chanel']);
                
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
                
                // Process image - using DLE's default no-image path
                if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $row['full_story'], $img_match)) {
                    $tpl->set('{image-1}', $img_match[1]);
                } else {
                    // Use DLE's default no-image path
                    $tpl->set('{image-1}', $config['http_home_url'] . 'templates/' . $config['skin'] . '/dleimages/no_image.jpg');
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
                
                // Compile the template
                $tpl->compile('content');
                
                // Output the content
                echo $tpl->result['content'];
                die();
            }
        }
    }
}

?>