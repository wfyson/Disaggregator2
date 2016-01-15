<?php

class ComponentBrowser extends tauAjaxXmlTag
{
        private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');
                
                $this->person = $person;			
	}

        public function showComponents(Descriptor $descriptor, $complete=false)
        {   
            $this->setData("");
            $this->descriptor = $descriptor;
            
            //show the components
            $this->addChild($this->components = new tauAjaxXmlTag('div'));
            $this->components->addClass("col-md-5"); 
            $this->components->addChild(new tauAjaxHeading(2, $this->descriptor->Name . "s"));
            $this->components->addChild($this->componentList = new ComponentList());
                                            
            $this->componentList->showComponents($this->descriptor, $complete);
                       
            //add a component viewer for more detail
            $this->addChild($this->componentViewer = new ComponentViewer());
            $this->componentViewer->addClass("col-md-5 col-md-offset-2");
            $this->attachEvent("show_component", $this, "e_show_component");
        }
        
        public function e_show_component(tauAjaxEvent $e)
        {
            $this->componentViewer->showComponent($e->getParam('component'));
        }
}

class ComponentList extends ListGroup
{
    public function showComponents(Descriptor $descriptor, $complete=false)
    {
        $this->setData('');		

        $components = Component::getComponents($descriptor, $complete);
        
        foreach($components as $component)
        {
            $this->addComponent($component);
            
        }        
    }
        
    public function addComponent(Component $component)
    {
        $this->addChild(new ComponentListItem($component));
    }
}

class ComponentListItem extends ListGroupItem
{
    private $component;
    
    public function __construct(Component $component)
    {
        parent::__construct($component->getPreviewText());
        
        $this->component = $component;
        
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->runJS("$('.active').removeClass('active');");
        
        $this->addClass("active");
        $this->triggerEvent("show_component", array("component" => $this->component)); 
        $this->triggerEvent("update_builder", array("value"=> $this->component->ComponentID));
    }
}

class ComponentViewer extends tauAjaxXmlTag        
{
    private $component;
    
    public function __construct()
    {
        parent::__construct('div');                      
    }    
    
    public function showComponent(Component $component)
    {      
        $model = DisaggregatorModel::get();
        $this->component = $component;
        $this->setData("");    
        
        $descriptor = $this->component->getDescriptor();
        $descriptorFields = $descriptor->getdescriptorfields();
        $i = $descriptorFields->getIterator();
        while($i->hasNext())
        {
            $descriptorField = $i->next();
            $field = $descriptorField->getDisaggregatorField();
            
            $fieldValue = $this->component->getFieldValues($field)[0];
            if($fieldValue)
            {
                $value = $fieldValue->Value;
            }
            else
            {
                $value = "N/A";
            }
            
            switch($field->Type)
            {
                case "Text":                    
                    $this->addChild(new tauAjaxLabel($this->field_value = new tauAjaxSpan($value), $field->Name));
                    $this->addChild($this->field_value);
                    $this->field_value->addClass("TauAjaxReadOnlyInput form-control");
                    break;
                case "File":                
                    $this->addChild(new tauAjaxLabel($this->field_value = new tauAjaxSpan($value), $field->Name));
                    $this->addChild($this->field_value);
                    $this->field_value->addClass("TauAjaxReadOnlyInput form-control");
                    break;
                case "Component":
                    if($value != "N/A")
                    {                        
                        $componentValue = $model->component->getRecordByPK($value);                    
                        $value = $componentValue->getPreviewText();
                    }
                    $this->addChild(new tauAjaxLabel($this->field_value = new tauAjaxSpan($value), $field->Name));
                    $this->addChild($this->field_value);
                    $this->field_value->addClass("TauAjaxReadOnlyInput form-control");
                    break;
            }
        }
    }    
}



?>
