<h1>You have been logged out!</h1>

<?php

tauAjaxServerHandler::clear(true);
$_SESSION = array();

$conf = iotaConf::getInstance();

setcookie($conf->auth_cookie_name, '', time() - 3600, '/', false, true);

$this->file->setDecoration('user', false);

?>
