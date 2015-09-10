<?php

class PortfolioUI extends tauAjaxXmlTag
{

    private $contributor;

    public function __construct(Contributor $contributor = null)
    {
        parent::__construct('div');

        if($contributor != null)
        {
            $this->addChild($this->header = new BootstrapHeader($contributor->getName() . "'s Portfolio"));

            $this->contributor = $contributor;

            $this->attachEvent('init', $this, 'e_init');
            $this->attachEvent('refresh', $this, 'e_refresh');
        }
        else
        {
            $this->addChild(new BootstrapAlert("No contributor selected!", "alert-danger"));
        }
    }

    public function e_init(tauAjaxEvent $e)
    {        
        //tabs
        $this->addChild($this->tabs = new BootstrapTabs("portfolio"));
        
        //document tab
        $documents = new BootstrapTabPane("Documents", "documents");
        $documents->addChild(new tauAjaxHeading(3, "Documents"));
        $documents->addChild($this->documentPortfolio = new DocumentPortfolio());
        $this->tabs->addTab($documents);
        
        //contributions tab
        $contributions = new BootstrapTabPane("Contibutions", "contributions");
        $contributions->addChild(new tauAjaxHeading(3, "Contributions"));
        $contributions->addChild($this->contributionPortfolio = new ContributionPortfolio());
        $this->tabs->addTab($contributions);
        
        $this->runJS("
                    $('#portfolio a:first').tab('show');
            ");
        
        $this->triggerEvent("refresh");
    }
    
    public function e_refresh(tauAjaxEvent $e)
    {
	$this->documentPortfolio->showDocuments($this->contributor->getDocuments());                
    }

}

?>
