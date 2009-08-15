<?
/*
# Don't show .git or .svn folders publicly
RedirectMatch 404 /\\.git(/|$)
<FilesMatch "\.(php)$">
php_value auto_prepend_file /home/tchalvak/ninjawars/deploy/lib/base.inc.php
</FilesMatch>
 */
?>
