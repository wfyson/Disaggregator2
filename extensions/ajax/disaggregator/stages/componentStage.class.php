<?php

class ComponentStage extends BuilderStage implements DisaggregatorStage
{    
    public function __construct(Component $component, Field $field)
    {     
        parent::__construct($component, $field);
        
        $model = DisaggregatorModel::get();
        $this->descriptor = $model->descriptor->getRecordByPK($field->DescriptorType);
        
        $this->addChild(new tauAjaxHeading(4, $this->field->Name));
        $this->addChild(new tauAjaxLabel($this->component_input = new tauAjaxSpan(), "Select a " . $this->descriptor->Name . ":"));
        $this->addChild($this->component_input);
        $this->component_input->addClass("TauAjaxReadOnlyInput form-control");
        
        $this->addChild($this->btn_delete = new BootstrapButton("", "btn-danger"));
        $this->btn_delete->addChild(new Glyphicon("trash"));
        $this->btn_delete->attachEvent("onclick", $this, "e_delete");
        
        if($field->Multi)
        {
            $this->addScroller();
        }  
        
        //set or create a fieldvalue as appropriate
        if(!($this->fieldValues[$this->record]))
        {
            $this->fieldValues = array();
            $this->fieldValues[$this->record] = $this->newFieldValue();
        }
        else
        {
            $this->setValue($this->fieldValues[$this->record]->Value);
        }
    }
    
    public function newFieldValue()
    {
        $model = DisaggregatorModel::get();
        $fieldValue = $model->componentvalue->getNew();
        $fieldValue->ComponentID = $this->component->ComponentID;                 
        $fieldValue->FieldID = $this->field->FieldID;                 
        
        return $fieldValue;
    }
    
    public function setValue($value)
    {        
        $model = DisaggregatorModel::get();
        
        if($value)
        {
            $component = $model->component->getRecordByPK($value);
            $this->component_input->setData($component->getPreviewText());
        }
        else
        {
            $this->component_input->setData("");
        }
        
        $this->fieldValues[$this->record]->Value = $value; 
    }
    
    public function storeCurrentValue()
    {
        //do nothing?
    }
    
    public function e_delete(tauAjaxEvent $e)
    {
        $fieldValue = $this->getCurrentRecord();
        $fieldValue->delete();
        
        $this->fieldValues[$this->record] = $this->newFieldValue();
        $this->component_input->setData("");
    }

    public function trigger()
    {
        parent::trigger();
        
        //hide document viewer and show a list of relevant components instead        
        $this->triggerEvent("show_components", array("descriptor"=>$this->descriptor));            
        
    }
    
    public function isComplete()
    {                                              
        //save all of the recorded fields 
        foreach($this->fieldValues as $record => $fieldValue)
        {                       
            if($fieldValue->Value)
            {
                $fieldValue->save();
            }
        }                         
        return true;
    }
}



?>
