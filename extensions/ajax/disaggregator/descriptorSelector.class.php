<?php

class DescriptorSelector extends tauAjaxXmlTag
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
        $model = DisaggregatorModel::get();
        
        $this->addChild(new BootstrapAlert("No component has been select! Please select a component to extract from the document.", "alert-danger"));
        
        $descriptors = $model->descriptor->getRecords();
        $i = $descriptors->getIterator();
        
    }            
}



?>
