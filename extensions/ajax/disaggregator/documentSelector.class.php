<?php

class DocumentSelector extends tauAjaxXmlTag
{

    private $person;

    public function __construct(DisaggregatorPerson $person)
    {
	parent::__construct('div');

        $this->person = $person;

        $this->attachEvent('init', $this, 'e_init');                        
    }
        
    public function e_init(tauAjaxEvent $e)
    {    
        $this->addChild(new BootstrapAlert("No document has been select! Please select or upload a document.", "alert-danger"));
    }            
}



?>
