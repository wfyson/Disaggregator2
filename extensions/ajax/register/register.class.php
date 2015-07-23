<?php

class RegisterUI extends tauAjaxXmlTag
{
    public function __construct()
    {
        parent::__construct('div');
        
        $this->attachEvent('init', $this, 'e_init');
    }
    
    public function e_init(tauAjaxEvent $e)
    {
        $this->addChild($this->header = new BootstrapHeader("Register"));  
        $this->addChild($this->register = new RegisterForm());
    }
    
}

class RegisterForm extends tauAjaxXmlTag
{
        private $person;
    
	public function __construct(DisaggregatorPerson $person=null)
	{
            parent::__construct('div');
            
            if(isset($person))
            {
                $this->person = $person;
                $this->contributor = $person->getcontributor();
            }
            else
                $this->person = DisaggregatorModel::get()->person->getNew(); 
            
            $this->init();
	}
        
        public function init()
        {          
            $this->addChild($this->registerForm = new tauAjaxXmlTag('div'));
            $this->addClass('col-md-6');
            
            //given name input
            $this->registerForm->addChild(new tauAjaxLabel($this->given = new tauAjaxTextInput(), "Given Name"));
            $this->registerForm->addChild($this->given); 
            $this->given->setValue($this->contributor->GivenName);
            
            //family name input
            $this->registerForm->addChild(new tauAjaxLabel($this->family = new tauAjaxTextInput(), "Family Name"));
            $this->registerForm->addChild($this->family);
            $this->family->setValue($this->contributor->FamilyName);
            
            //username input
            if($this->person->isNew())
            {
                $this->registerForm->addChild(new tauAjaxLabel($this->username = new tauAjaxTextInput(), "Username"));
                $this->registerForm->addChild($this->username);
            }
            
            //email input
            $this->registerForm->addChild(new tauAjaxLabel($this->email = new tauAjaxTextInput(), "Email"));
            $this->registerForm->addChild($this->email);
            $this->email->setValue($this->person->Email);
                        
            //password input
            if($this->person->isNew())
            {
                $this->registerForm->addChild(new tauAjaxLabel($this->password = new tauAjaxPasswordInput(), "Password"));
                $this->registerForm->addChild($this->password);
            }
            
            //save button
            $this->addChild($this->btn_save = new BootstrapButton("Save", "btn-primary"));
            $this->btn_save->attachEvent("onclick", $this, "e_save");
            
            //styling for the editor
            $this->runJS("                
                $('.tauAjaxTextInput').addClass('form-control');
                $('.tauAjaxPasswordInput').addClass('form-control');
                $('.tauAjaxSelect').addClass('form-control');                 
                $('button').addClass('btn btn-primary');
            ");
        }                
        
        public function e_save(tauAjaxEvent $e)
        {            
            if($this->person->isNew())
            {
                $this->person->Username = $this->username->getValue();
                $this->person->Password = $this->person->hash($this->password->getValue());
            }
            
            $this->person->Email = $this->email->getValue();
            $this->person->save();
            
            //and then try and create a contributor            
            $this->contributor->GivenName = $this->given->getValue();
            $this->contributor->FamilyName = $this->family->getValue();
            $this->contributor->UserID = $this->person->UserID;
            
            $this->contributor->save();
        }

}



?>
