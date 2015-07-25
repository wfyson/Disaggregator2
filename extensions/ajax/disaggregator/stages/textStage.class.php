<?php

class TextStage extends BuilderStage
{  
        
    public function __construct(Component $component, Field $field)
    {
        parent::__construct($component, $field);
        
        //create the interface
        $this->addChild(new tauAjaxLabel($this->txt_input = new tauAjaxTextInput(), "$field->Name: "));
        $this->addChild($this->txt_input);
        
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
        $this->fieldValue = $model->textvalue->getNew();
        $this->fieldValue->ComponentID = $this->component->ComponentID;                 
        $this->fieldValue->FieldID = $this->field->FieldID;                 
    }
    
    public function setValue($value)
    {
        error_log("setting the value");
        $this->txt_input->setValue($value);
        
    }
        
    public function isComplete()
    {                           
        
        $this->fieldValue->Value = $this->txt_input->getValue();
        $value = $this->fieldValue->Value;
        error_log("$value");
        //if a value has been set, save the field        
        if(isset($value))
        {
            error_log("yeah yeah");
            $this->fieldValue->save();
        }
        return true;
    }
}



?>
