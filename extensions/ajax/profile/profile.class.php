<?php

class ProfileUI extends tauAjaxXmlTag
{
        private $person;
    
	public function __construct(DisaggregatorPerson $person)
	{
            parent::__construct('div');

            $this->addChild($this->header = new BootstrapHeader("Profile"));  
                
            $this->person = $person;
            
            $this->attachEvent('init', $this, 'e_init');
	}
        
        public function e_init(tauAjaxEvent $e)
        {          
            $this->addChild($this->registerForm = new RegisterForm($this->person));           
        }                               

}



?>
