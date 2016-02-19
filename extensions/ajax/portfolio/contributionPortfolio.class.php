<?php

class ContributionPortfolio extends BootstrapTable
{

    private $contributor;

    public function __construct(Contributor $contributor)
    {
        parent::__construct();

        $this->contributor = $contributor;
    }

    public function showContributions($contributions)
    {        
        $this->body->setData('');
        $noContributions = 0;
        foreach($contributions as $contribution)
        {        
                $noContributions++;
                $this->addContribution($contribution);         
        }
        if($noContributions == 0)
        {
            $this->body->addChild(new EmptyRow("contributions"));
        }
    }

    public function addContribution(Component $contribution)
    {
        return $this->body->addChild(new ContributionPortfolioRow($contribution, $this->contributor));
    }

}

class ContributionPortfolioRow extends tauAjaxXmlTag
{

    private $component;
    private $contributor;

    public function __construct(Component $component, Contributor $contributor)
    {
        parent::__construct("tr");

        $this->component = $component;
        $this->contributor = $contributor;

        $this->init();
    }

    public function init()
    {
        $this->setData("");

        //name and roles
        $this->addChild($this->cell_name = new tauAjaxXmlTag("td"));
        $roles = $this->component->getContributorRoles($this->contributor);
        
        $entry = $this->component->getPreviewText();
        if($roles)
            $entry .= " (" . implode(", ", $roles) . ")";
        $this->cell_name->addChild($this->span_name = new tauAjaxSpan($entry));
        $this->span_name->addClass("h4");

        //view button
        $this->addChild($this->cell_view = new tauAjaxXmlTag("td"));
        $this->cell_view->addChild(new BootstrapLinkButton("View", "/component&component=" . $this->component->ComponentID, 'btn-primary'));
    }

}

?>
