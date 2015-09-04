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

$model = DisaggregatorModel::get();

//try and get a document if one is present
if(isset($_GET['document']))
{
    $document = $model->document->getRecordByPK($_GET['document']) ? $model->document->getRecordByPK($_GET['document']) : null;
}
elseif(isset($_GET['url']))
{
    //try and get document from a URL
}
elseif(!isset($document))
{
    $document = null;
}

$redactorUI = new RedactorUI($user, $document);

tauAjaxServerHandler::initElement($redactorUI, 'disaggregator.ajax.php');	



?>
