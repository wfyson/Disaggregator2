<?php

class TextStage extends BuilderStage implements DisaggregatorStage
{  
        
    public function __construct(Component $component, Field $field)
    {
        parent::__construct($component, $field);
        
        //create the interface
        $this->addChild(new tauAjaxHeading(4, $this->field->Name));
        
        $this->addChild(new tauAjaxLabel($this->txt_input = new tauAjaxTextInput(), "Select Text: "));
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
        $this->txt_input->setValue($value);        
    }
        
    public function isComplete()
    {                           
        
        $this->fieldValue->Value = $this->txt_input->getValue();
        $value = $this->fieldValue->Value;
        //if a value has been set, save the field        
        if($value != "")
        {
            $this->fieldValue->save();
        }
        return true;
    }
}



?>
