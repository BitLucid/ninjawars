<?php
/*
 *
 * @desc Redirect... Try PHP header redirect, then Java redirect, then try http redirect.:
 * @param $url Full url to redirect to.
 */
function redirect($url){
    if (!headers_sent()){ //If headers not sent yet... then do php redirect
        header('Location: '.$url);
        exit;
    }else{ //If headers were already sent... do javascript redirect... if javascript disabled, do html redirect.
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
        exit;
    }
}
?>
