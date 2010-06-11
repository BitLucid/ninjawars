<?php

function display_static_page($page, $pages, $vars=array(), $options=array()){
    if(!isset($pages[$page])){
        // Unlisted page requested.
        error_log('  Invalid page ('.$page.') requested on page.php.');
        display_page('404.tpl', '404'); 
    } else {
        if(!is_array($pages[$page])){
            $template = "page.".$page.".tpl";
            $title = $page; // Display_page will prepend with 'Ninja Wars: '
        } else {
            $page_info = $pages[$page];
            $template = first_value(@$page_info['template'], "page.".$page.".tpl");
            $title = $page_info['title'];

            $callback = @$page_info['callback'];
            if($callback && function_exists($callback)){
                $vars = $callback(); // Call the callback to return the vars.
            }            
        }
        
        display_page($template, $title, $vars, $options);
    }
}


?>
