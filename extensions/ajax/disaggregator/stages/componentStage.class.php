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
        
        //set or create a fieldvalue as appropriate
        if(!($this->fieldValue))
        {
            $this->newFieldValue();
        }
        else
        {
            $this->setValue($this->fieldValue->Value);
        }               
    }
    
    public function newFieldValue()
    {
        $model = DisaggregatorModel::get();
        $this->fieldValue = $model->componentvalue->getNew();
        $this->fieldValue->ComponentID = $this->component->ComponentID;                 
        $this->fieldValue->FieldID = $this->field->FieldID;                 
    }
    
    public function setValue($value)
    {
        $this->fieldValue->Value = $value;
        
        $model = DisaggregatorModel::get();
        $component = $model->component->getRecordByPK($value);
        $this->component_input->setData($component->getPreviewText());
    }
    
    public function trigger()
    {
        parent::trigger();
        
        //hide document viewer and show a list of relevant components instead        
        $this->triggerEvent("show_components", array("descriptor"=>$this->descriptor));            
        
    }
    
    public function isComplete()
    {                                   
        $value = $this->fieldValue->Value;
        //if a value has been set, save the field        
        if(isset($value))
        {
            $this->fieldValue->save();
        }
        return true;
    }
}



?>
