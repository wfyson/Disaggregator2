<?php

class DescriptorSelector extends tauAjaxXmlTag
{

    private $person;
    private $selected;

    public function __construct(DisaggregatorPerson $person)
    {
	parent::__construct('div');

        $this->person = $person;

        $this->init();                        
    }
        
    public function init()
    {                               
        $this->addChild(new BootstrapAlert("No component has been select! Please select a component to extract from the document.", "alert-danger"));
        
        //selector
        $this->addChild($this->descriptorSelect = new tauAjaxXmlTag("div"));
        $this->descriptorSelect->addClass("col-md-4");
        
        $this->descriptorSelect->addChild(new tauAjaxHeading(2, "Select a compound"));
        
        $this->descriptorSelect->addChild($this->descriptorList = new DescriptorList());        
        $model = DisaggregatorModel::get();
        $descriptors = $model->descriptor->getRecords();
        $this->descriptorList->showDescriptors($descriptors);
        
        $this->descriptorSelect->addChild($this->btn_select = new BootstrapButton("Select", "btn-primary"));
        
        //preview        
        $this->addChild($this->descriptorPreview = new DescriptorPreview());
        $this->descriptorPreview->addClass("col-md-6 col-md-offset-2");
        
        $this->attachEvent("show_descriptor", $this, "e_select_descriptor");        
        $this->btn_select->attachEvent("onclick", $this, "e_select");  
    }            
    
    public function e_select_descriptor(tauAjaxEvent $e)
    {
        $this->selected = $e->getParam("descriptor");
        $this->descriptorPreview->showDescriptor($this->selected);
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->triggerEvent("descriptor_select", array("descriptor"=> $this->selected));
    }
}

class DescriptorPreview extends tauAjaxXmlTag        
{
    private $descriptor;
    
    public function __construct()
    {
        parent::__construct('div');
    }
    
    public function showDescriptor(Descriptor $descriptor)
    {
        $model = DisaggregatorModel::get();
        
        $this->descriptor = $descriptor;
        $this->setData("");
        
        //name and description
        $this->addChild($this->descriptorPanel = new BootstrapPanel($descriptor->Name, "panel-info"));
        $this->descriptorPanel->addBody(new tauAjaxSpan($descriptor->Description));
             
        //$creator = $model->person->getRecordByPK($descriptor->UserID);
        //$this->addChild(new tauAjaxSpan("Created by $creator->username"));
        
        //add the fields
        $this->addChild(new tauAjaxLabel($this->fieldList = new FieldList($this->descriptor), "Fields"));
        $this->addChild($this->fieldList);        
        
    }    
}



?>
