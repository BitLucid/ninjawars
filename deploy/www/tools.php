<?php
$private    = false;
$alive      = true;
$quickstat  = null;
$page_title = "Web Administration Tools";

include SERVER_ROOT."interface/header.php";
?>


<h1>Web Administration Tools</h1>

<ul>
    <li>
        <a href='http://validator.w3.org/check?uri=referer' target='_blank' class='extLink'>Check HTML Validation</a>
    </li>
    <li>
        <a href='http://jigsaw.w3.org/css-validator/validator?uri=http://ninjawars.net&profile=css21&usermedium=all&warning=1&lang=en' target='_blank' class='extLink'>Check Validation of CSS</a>
    </li>
    <li>
        <a href='http://validator.w3.org/checklink?uri=http://www.ninjawars.net&hide_type=all&depth=&check=Check' target='_blank' class='extLink'>Check ninjawars for broken links</a>
    </li>
</ul>

<?php
include SERVER_ROOT."interface/footer.php";
?>
