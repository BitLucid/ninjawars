<?php
if ( $_POST['register'] ) {
    require_once('Validator.php');

    // Register the subclasses to use
    $v['u']=new ValidateUser($_POST['user']);
    $v['p']=new ValidatePassword($_POST['pass'],$_POST['conf']);
    $v['e']=new ValidateEmail($_POST['email']);

    // Perform each validation
    foreach($v as $validator) {
        if (!$validator->isValid()) {
            while ($error=$validator->getError()) {
                $errorMsg.="<li>".$error."</li>\n";
            }
        }
    }
    if (isset($errorMsg)) {
        print ("<p>There were errors:<ul>\n".$errorMsg."</ul>");
    } else {
        print ('<h2>Form Valid!</h2>');
    }
} else {
?>
<h2>Create New Account</h2>
<form action="<?php echo ($_SERVER['PHP_SELF']); ?>" method="post">
<p>Username: <input type="text" name="user"></p>
<p>Password: <input type="password" name="pass"></p>
<p>Confirm: <input type="password" name="conf"></p>
<p>Email: <input type="text" name="email"></p>
<p><input type="submit" name="register" value=" Register "></p>
</form>
<?php
}
?>
