<?php
function smarty_modifier_to_tags($str_tags) {
    $tags = array();

    if (strpos($str_tags, ',') !== false) {
        $tags = explode(',', $str_tags);
    } elseif ( ! empty($str_tags)) {
        $tags = array($str_tags);
    }

    if (empty($tags)) {
        return '-';
    } else {
        $str_tags = '';
        foreach ($tags as $tag) {
            // Build tag anchors
            $tag = trim($tag);
            $str_tags .= '<a href="news.php?tag_query='.htmlentities(url($tag)).'" target="main">#'.htmlentities($tag).'</a> ';
        }

        return $str_tags;
    }
}
