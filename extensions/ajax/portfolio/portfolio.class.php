<?php

class PortfolioUI extends tauAjaxXmlTag
{

    private $contributor;

    private $helpText = "
        <p>Your portfolio as seen by other visitors to the site!</p>
        <p>'Documents' are your documents that have been uploaded to the Disaggregator and are available to download.</p>
        <p>'Components' shows the items you've extracted from your documents and have set to be publicly visible.</p>
        <p>'Contributions' shows those items other users may have listed you as a contributor towards.</p>
    ";
    
    public function __construct(Contributor $contributor = null, $loggedIn = false)
    {
        parent::__construct('div');

        if($contributor != null)
        {
            $this->addChild($this->header = new BootstrapHeader($contributor->getName() . "'s Portfolio",
                    $contributor->Orcid));

            $this->contributor = $contributor;
            $this->loggedIn = $loggedIn;
            
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
        if($this->loggedIn)
        {
            HelperUtil::addHelpGlyph($this->header->getHeader(), "bottom", $this->helpText);
            HelperUtil::initHelpGlyph($this);
        }
        
        //tabs
        $this->addChild($this->tabs = new BootstrapTabs("portfolio"));
        
        //document tab
        $documents = new BootstrapTabPane("Documents", "documents");
        $documents->addChild(new tauAjaxHeading(3, "Documents"));
        $documents->addChild($this->documentPortfolio = new DocumentPortfolio());
        $this->tabs->addTab($documents);
        
        //contributions tab
        $components = new BootstrapTabPane("Components", "components");
        $components->addChild(new tauAjaxHeading(3, "Components"));
        $components->addChild($this->componentPortfolio = new ContributionPortfolio($this->contributor));
        $this->tabs->addTab($components);
        
        //contributions tab
        $contributions = new BootstrapTabPane("Contributions", "contributions");
        $contributions->addChild(new tauAjaxHeading(3, "Contributions"));
        $contributions->addChild($this->contributionPortfolio = new ContributionPortfolio($this->contributor));
        $this->tabs->addTab($contributions);
        
        $this->runJS("
                    $('#portfolio a:first').tab('show');
            ");
        
        $this->triggerEvent("refresh");
    }
    
    public function e_refresh(tauAjaxEvent $e)
    {
        $person = $this->contributor->getPerson();
        if($person)           
            $this->documentPortfolio->showDocuments($person->getDocuments(false, "Public"));
        else
        {
            $this->documentPortfolio->body->addChild(new EmptyRow("documents"));
        }        
        
        //components user has disaggregated
        $this->componentPortfolio->showContributions($this->contributor->getUserComponents()); 
        
        //contributions
        $this->contributionPortfolio->showContributions($this->contributor->getComponents());    
    }       
}

class EmptyRow extends tauAjaxXmlTag
{
    public function __construct($name)
    {
        parent::__construct("tr");        
        $this->addChild($this->cell_no = new tauAjaxXmlTag("td"));        
        $this->cell_no->addChild(new tauAjaxHeading(4, "No $name to show."));
    }
}

?>
