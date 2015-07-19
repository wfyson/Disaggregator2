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
else
{
    $document = null;
}

//try and get a descriptor if one is present
if(isset($_GET['descriptor']))
{
    $descriptor = $model->descriptor->getRecordByPK($_GET['descriptor']) ? $model->descriptor->getRecordByPK($_GET['descriptor']) : null;
}
else
{
    $descriptor = null;
}

$disaggregatorUI = new DisaggregatorUI($user, $document, $descriptor);

tauAjaxServerHandler::initElement($disaggregatorUI, 'disaggregator.ajax.php');	



?>
