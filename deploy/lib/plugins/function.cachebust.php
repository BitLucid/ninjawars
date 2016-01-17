<?php
/**
 * Smarty plugin to add a timestamp to static files for cachebusting
 *
 * @param Array $p_params Dictionary provided by Smarty
 * @return String
 */
function smarty_function_cachebust($p_params) {
    $file = ROOT."/www/$p_params[file]";

    if (is_file($file)) {
        $mtime = filemtime($file);
        $pathParts = pathinfo($p_params['file']);
        return $pathParts['dirname'].'/'.$pathParts['filename'].".$mtime.".$pathParts['extension'];
    } else {
        return $p_params['file'];
    }
}
