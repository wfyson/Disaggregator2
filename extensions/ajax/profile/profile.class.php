<?php

class ProfileUI extends tauAjaxXmlTag
{
        private $person;
    
        private $helpText = "
            <p>Edit your personal details, including your email address and ORCID</p>
            <p>Use the portfolio link to direct people to your portfolio where they can view the research outputs you've chosen to make available.</p>
        ";
        
	public function __construct(DisaggregatorPerson $person)
	{
            parent::__construct('div');

            $this->addChild($this->header = new BootstrapHeader("Profile"));  
                
            $this->person = $person;
            
            $this->attachEvent('init', $this, 'e_init');
	}
        
        public function e_init(tauAjaxEvent $e)
        {          
            HelperUtil::addHelpGlyph($this->header->getHeader(), "bottom", $this->helpText);
            HelperUtil::initHelpGlyph($this);
            
            $this->addChild($this->registerForm = new RegisterForm($this->person)); 
            
            $this->addChild($this->right = new tauAjaxXmlTag('div'));
            $this->right->addClass("col-md-5 col-md-offset-1");
            
            $this->right->addChild(new tauAjaxLabel($this->input_portfolio = new BootstrapTextInput(), "Portfolio"));
            $this->input_portfolio->setValue($this->person->getPortfolioLink());
            $this->input_portfolio->addClass("portfolio_link");
            $this->input_portfolio->setAttribute("readonly", "readonly");
            $this->right->addChild($this->input_portfolio);
        }                               

}



?>
