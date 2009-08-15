<?
/*


# Don't show .git folders publicly
RedirectMatch 404 /\\.git(/|$)
<FilesMatch "\.(php)$">
php_value auto_prepend_file "/home/tchalvak/ninjawars/deploy/lib/base.inc.php"
</FilesMatch>
#Note that the AllowOverride directive in /etc/apache2/sites-available/default
#cannot equal None, as that will prevent the .htaccess from working.


 */
?>
