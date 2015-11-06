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
                $this->registering = false;
            }
            else
            {
                $this->person = DisaggregatorModel::get()->person->getNew();
                $this->contributor = DisaggregatorModel::get()->contributor->getNew();
                $this->registering = true;
            }
                
            $this->init();
	}
        
        public function init()
        {          
            $this->setData("");
            $this->addChild($this->alertDiv = new tauAjaxXmlTag('div'));
            $this->addChild($this->registerForm = new tauAjaxXmlTag('div'));
            $this->addClass('col-md-6');
            
            //given name input
            $this->registerForm->addChild(new tauAjaxLabel($this->given = new BootstrapTextInput(), "Given Name*"));
            $this->registerForm->addChild($this->given); 
            $this->given->setValue($this->contributor->GivenName);
            
            //family name input
            $this->registerForm->addChild(new tauAjaxLabel($this->family = new BootstrapTextInput(), "Family Name*"));
            $this->registerForm->addChild($this->family);
            $this->family->setValue($this->contributor->FamilyName);
            
            //username input
            $this->registerForm->addChild(new tauAjaxLabel($this->username = new BootstrapTextInput(), "Username*"));
            $this->registerForm->addChild($this->username);
            if(!$this->person->isNew())
            {
                $this->username->setAttribute("readonly");
                $this->username->setValue($this->person->Username);
            }
            
            //email input
            $this->registerForm->addChild(new tauAjaxLabel($this->email = new BootstrapTextInput(), "Email"));
            $this->registerForm->addChild($this->email);
            $this->email->setValue($this->person->Email);
            
            //orcid input
            $this->registerForm->addChild(new tauAjaxLabel($this->orcid = new BootstrapTextInput(), "ORCID"));
            $this->registerForm->addChild($this->orcid);
            $this->orcid->setValue($this->contributor->Orcid);
                        
            //password input
            if($this->person->isNew())
            {
                $this->registerForm->addChild(new tauAjaxLabel($this->password = new BootstrapPasswordInput(), "Password*"));
                $this->registerForm->addChild($this->password);
            }
            
            //save button
            $this->addChild($this->btn_save = new BootstrapButton("Save", "btn-primary"));
            $this->btn_save->attachEvent("onclick", $this, "e_save");
        }                
        
        public function e_save(tauAjaxEvent $e)
        {            
            $missingFields = array();
            if($this->given->getValue() == "")
            {
                $missingFields[] = "given name";
            }
            if($this->family->getValue() == "")
            {
                $missingFields[] = "family name";
            }
            if($this->username->getValue() == "")
            {
                $missingFields[] = "username";
            }
            if($this->person->isNew())
            {
                if($this->password->getValue() == "")
                {
                    $missingFields[] = "password";
                }
            }
            if(count($missingFields) > 0)
                return $this->throwError("Missing fields: " . implode(", ", $missingFields));
            
            //check username is unique
            if($this->person->isNew())
            {
                if(!DisaggregatorPerson::isUsernameUnique($this->username->getValue()))
                {
                    return $this->throwError("Username unavailable. Please choose a new username.");
                }
            }
            
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
            $this->contributor->Orcid = $this->orcid->getValue();
            $this->contributor->UserID = $this->person->UserID;
            
            $this->contributor->save();
            
            /*
             * CONTENT FOR DEMO REMOVE AT LATER DATE
             */
            if($this->person->isNew())
            {
            $demo_user = DisaggregatorPerson::getUserById("demo");
            $demo_docs = $demo_user->getdocuments();
            $i = $demo_docs->getIterator();
            while($i->hasNext())
            {
                $doc = $i->next();
                
                //create a new document entry
                $new_doc = DisaggregatorModel::get()->document->getNew();
                
                $new_doc->Name = $doc->Name;
                $new_doc->Filepath =$doc->Filepath;
                $new_doc->UserID = $this->person->UserID;
                $new_doc->Security = "User";
                $new_doc->Source = "Demo";                
                
                $new_doc->save();
            }
            /*
             * END DEMO CONTENT
             */
            }
            
            if($this->registering)
            {
                $this->showAlert(new BootstrapAlert("Thank you for registering! Now you can <a href='/'>log in</a>.", "alert-success"));
            }
        }
        
        public function throwError($error)
        {
            $this->showAlert(new BootstrapAlert($error, "alert-danger"));
        }
        
        public function showAlert(BootstrapAlert $alert)
        {
            $this->alertDiv->setData("");
            $this->alertDiv->addChild($alert);                    
        }

}



?>
