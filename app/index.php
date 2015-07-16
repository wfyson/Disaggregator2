<?php

// Require a user ig login is requested
if(array_key_exists('login', $_GET))
{
	if(!disaggregatorUtil::requireUser())
	{
		return false;
	}
}

if(!disaggregatorUtil::requireUser())
{
	return false;
}

try
{
    $user = tauFile::getCurrentFile()->getDecoration('user');
}
catch(iotaException $e)
{
    debug_print_backtrace();
}

$documentBrowser = new DocumentBrowser($user);

tauAjaxServerHandler::initElement($documentBrowser, 'disaggregator.ajax.php');	



?>
