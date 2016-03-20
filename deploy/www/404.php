<?php
header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true, 404 );
echo render_template('404.tpl');