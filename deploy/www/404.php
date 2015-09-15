<?php
header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true, 404 );
display_template('404.tpl');