<?php

try
{
    $user = tauFile::getCurrentFile()->getDecoration('user');
}
catch(iotaException $e)
{
    debug_print_backtrace();
}

$model = DisaggregatorModel::get();

if(isset($_GET['contributor']))
{
    //if ORCID get contributor via ORCID otherwise get via PK    
    $identifier = $_GET['contributor'];
    if(OrcidHelper::validateOrcid($identifier))
    {
        $contributor = Contributor::getContributorByOrcid($identifier);
    }
    else
    {
        $contributor = $model->contributor->getRecordByPK($_GET['contributor']) ? $model->contributor->getRecordByPK($_GET['contributor']) : null;
    }   
}
else
{
    if($user->UserID != null)
    {        
        $contributor = $user->getContributor();
    }
}

$portfolioUI = new PortfolioUI($contributor);

tauAjaxServerHandler::initElement($portfolioUI, 'disaggregator.ajax.php');	



?>
