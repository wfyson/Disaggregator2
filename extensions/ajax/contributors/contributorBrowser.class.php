<?php

class ContributorBrowser extends tauAjaxXmlTag
{
        private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');
                
                $this->person = $person;			
	}

        public function showContributors()
        {   
            $this->setData("");
            
            //show the contributors
            $this->addChild($this->contributors = new tauAjaxXmlTag('div'));
            $this->contributors->addClass("col-md-5"); 
            $this->contributors->addChild(new tauAjaxHeading(2, "Contributors"));
            $this->contributors->addChild($this->contributorList = new ContributorList());
                                            
            $this->contributorList->showContributors();
                       
            //add a contributor adder
            $this->addChild($this->contributorAdder = new ContributorAdder($this->person));
            $this->contributorAdder->addClass("col-md-5 col-md-offset-2");
            
            $this->attachEvent("update_browser", $this, "e_update_browser");                        
        }
        
        public function e_update_browser(tauAjaxEvent $e)
        {
            $this->contributorList->showContributors($e->getParam("selected"));
        }
}

class ContributorList extends ListGroup
{
    public function showContributors($selected=NULL)
    {
        $this->setData('');		

        $contributors = Contributor::getContributors()->toArray();
        
        foreach($contributors as $contributor)
        {
            $item = $this->addContributor($contributor);       
            if($contributor->ContributorID == $selected)
                $item->addClass("active");
        }        
    }
        
    public function addContributor(Contributor $contributor)
    {
        $this->addChild($item = new ContributorListItem($contributor));
        return $item;
    }        
}

class ContributorListItem extends ListGroupItem
{
    private $contributor;
    
    public function __construct(Contributor $contributor)
    {
        parent::__construct($contributor->getName());
        
        if($contributor->Orcid != "")
        {
            $this->addChild($this->orcid = new tauAjaxSpan(" $contributor->Orcid"));
            $this->orcid->addClass("orcid");
        }
        
        $this->contributor = $contributor;
        
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->runJS("$('.active').removeClass('active');");
        
        $this->addClass("active");
        $this->triggerEvent("update_builder", array("value"=> $this->contributor->ContributorID));        
    }
}




?>
