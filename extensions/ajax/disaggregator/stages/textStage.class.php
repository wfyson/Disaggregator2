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
            $this->setValue($this->fieldValues[$this->record]->getPreview());
        }
    }
    
    public function newFieldValue()
    {
        $model = DisaggregatorModel::get();
        $fieldValue = $model->textvalue->getNew();
        $fieldValue->ComponentID = $this->component->ComponentID;                 
        $fieldValue->FieldID = $this->field->FieldID;                 
        
        return $fieldValue;
    }
    
    public function setValue($value)
    {        
        $this->txt_input->setValue($value);        
    }        
    
    public function storeCurrentValue()
    {                        
        $this->fieldValues[$this->record]->Value = $this->txt_input->getValue();
    }
        
    public function isComplete()
    {                               
        //store whatever we have saved 
        $this->storeCurrentValue();
        
        //save all of the recorded fields 
        foreach($this->fieldValues as $record => $fieldValue)
        {
            //if a value has been set, save the field        
            if($fieldValue->Value  != "")
            {
                $fieldValue->save();
            }
            else
            {                                
                $fieldValue->delete();
                $this->fieldValues[$record] = $this->newFieldValue();
            }
        }
        return true;
    }
}



?>
