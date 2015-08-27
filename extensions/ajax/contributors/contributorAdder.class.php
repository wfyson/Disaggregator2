<?php

class ContributorAdder extends tauAjaxXmlTag
{
        private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');
                
                $this->person = $person;	
                
                $this->init();
	}

        public function init()
        {                                
            $this->addChild(new tauAjaxHeading(2, "Add a new Contributor"));
            
            //orcid input
            $this->addChild($this->orcid_form_group = new BootstrapFormGroup(new tauAjaxLabel($this->orcid = new BootstrapTextInput(), "ORCID"), $this->orcid));                        
            $this->orcid->attachEvent("ontype", $this, "e_type");
            
            //given name input
            $this->addChild($this->given_form_group = new BootstrapFormGroup(new tauAjaxLabel($this->given = new BootstrapTextInput(), "Given Name"), $this->given));            
            
            //family name input
            $this->addChild($this->family_form_group = new BootstrapFormGroup(new tauAjaxLabel($this->family = new BootstrapTextInput(), "Family Name"), $this->family));            
            
            $this->addChild($this->btn_add = new BootstrapButton("Add Contributor", "btn-primary"));            
            $this->btn_add->attachEvent("onclick", $this, "e_new_contributor");
        }
        
        public function removeErrors()
        {
            $this->given_form_group->removeClass("has-error");
        }
        
        public function e_new_contributor(tauAjaxEvent $e)
        {
            $this->removeErrors();
            
            $model = DisaggregatorModel::get();
            $contributor = $model->contributor->getNew();
            
            //check we have everything and if so save the new contributor and add them to the builder stage
            //check given name
            if($this->given->getValue() != null)
            {
                $contributor->GivenName = $this->given->getValue();
            }
            else
            {
                $this->given_form_group->addClass("has-error");
                return;
            }
            
            //check family name
            if($this->family->getValue() != null)
            {
                $contributor->FamilyName = $this->family->getValue();
            }
            else
            {
                $this->family_form_group->addClass("has-error");
                return;
            }  
            
            if($this->orcid->getValue() != null)
                $contributor->Orcid = $this->family->getValue();
            
            //TODO: check if contributor with given orcid already exists before saving...
            $contributor->save();
            
            $this->triggerEvent("update_builder", array("value"=> $contributor->ContributorID));
            $this->triggerEvent("update_browser", array("selected"=>$contributor->ContributorID));
        }
        
        public function e_type(tauAjaxEvent $e)
        {
            $orcid = $this->orcid->getValue();
            if(OrcidHelper::validateOrcid($orcid))
            {
                //get their details and fill in the rest of the form
                $names = OrcidHelper::getDetails($orcid);
                                
                $this->given->setValue($names["given"]);
                $this->family->setValue($names["family"]);
            }
        }
}




?>
