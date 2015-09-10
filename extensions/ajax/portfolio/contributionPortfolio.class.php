<?php

class ContributionPortfolio extends BootstrapTable
{        
    public function showContributions(ADROSet $contributions)
	{
		$this->body->setData('');		

		$i = $contributions->getIterator();
		while($i->hasNext())
		{		
			$contribution = $i->next();
			$this->addContribution($contribution);
		}
	}

	public function addContribution(Component $contribution)
	{
		return $this->body->addChild(new ContributionPortfolioRow($contribution));
	}
}

class ContributionPortfolioRow extends tauAjaxXmlTag
{
	private $contribution;

	public function __construct(Component $contribution)
	{
            parent::__construct("tr");

            $this->contribution = $contribution;
                  
           
	}          

}

?>
