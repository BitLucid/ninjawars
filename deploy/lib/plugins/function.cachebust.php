<?php
/**
 * Smarty plugin to add a timestamp to static files for cachebusting
 */
function smarty_function_cachebust($p_params, &$p_tpl) {
    $file = $_SERVER['DOCUMENT_ROOT']."/$p_params[file]";
    if (is_file($file)) {
        $mtime = filemtime($file);
        $pathParts = pathinfo($p_params['file']);
        return $pathParts['dirname'].'/'.$pathParts['filename'].".$mtime.".$pathParts['extension'];
    } else {
        return $p_params['file'];
    }
}
