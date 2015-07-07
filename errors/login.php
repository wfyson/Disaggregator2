<?php

$unfield = iotaconf::getInstance()->get('tauUser_postFieldUn');
$passfield = iotaconf::getInstance()->get('tauUser_postFieldPass');
$redirfield = iotaconf::getInstance()->get('tauUser_postFieldRedir');

$conf = iotaconf::getInstance();

$url = $_SERVER['REQUEST_URI'];

$reason = $args;

switch($reason)
{
    case tauAuthProvider::REASON_BADID:
    case tauAuthProvider::REASON_BADAUTH:
        $err = 'Your username or password was incorrect';
        break;
    default:
        $err = false;
        break;
}

?>

	<div id="login-form">

    <div id="login">

        <h2>Login</h2>

        <form role="form" action="?" method="post" style="width: 50%;" id="iotalogin">
            <input type="hidden" name="login" value="true" />

            <?php if ($err !== false) { ?>
                <div class="errorMessage"><?php echo $err; ?></div>
            <?php } ?>

		<?php if($url !== false) { ?>
		<input type="hidden" name="<?php echo $redirfield; ?>" id="_login_form_redir" value="<?php echo htmlspecialchars($url); ?>" />
		<script type="text/javascript">
			// Replace server-suppled URL with local one, that should include the # if present
			document.getElementById('_login_form_redir').value = document.location.toString().replace(/\?login/, '?');
		</script>
	
	<?php } ?>
	

                <div class="form-group">
                    <label for="<?php echo $unfield; ?>">Username</label>
                    <input type="text" name="<?php echo $unfield; ?>" id="<?php echo $unfield; ?>" class="form-control" placeholder="Your username" autofocus maxlength="20" />
                </div>
                
                    <div class="form-group">
                    <label for="<?php echo $passfield; ?>">Password</label>
                    <input type="password" name="<?php echo $passfield; ?>" id="<?php echo $passfield; ?>" class="form-control" placeholder="Your password" maxlength="20" />
                    </div>

<span id="keeploggedin"><input type="checkbox" value="1"<?php echo $conf->auth_cookie_POST_flag; ?>" id="<?php echo $conf->auth_cookie_POST_flag; ?>" />
                <label for="<?php echo $conf->auth_cookie_POST_flag; ?>">Keep me logged in</label></span>

            <input class="btn btn-default" type="submit" name="login" value="Login" />
        </form>


        <div id="register">
            <h4>No account..?</h4> 
            <form action="index.php?action=register" method="post" style="width: 50%;">
                <input type="hidden" name="registerform" value="true" />
                <input class="btn btn-default" type="submit" name="registerform" value="Register" />
            </form>
        </div>

    </div>

</div>

<?php

?>
