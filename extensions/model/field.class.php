<?php

class Field extends adro 
{
    public function getTypeName()
    {
        if($this->Type == "Component")
        {
            $model = DisaggregatorModel::get();
            $descriptor = $model->descriptor->getRecordByPK($this->DescriptorType);            
            return $descriptor->Name;
        }
        else
        {
            return $this->Type;
        }        
    }
    
    public function saveFieldValue($value, $componentID)
    {
        $model = DisaggregatorModel::get();
        
        switch($this->Type)
        {
            case "Text":   
                $fieldValue = $model->textvalue->getNew();
                break;
            case "File":
                $fieldValue = $model->filevalue->getNew();
                break;
            case "Component":
                $fieldValue = $model->componentvalue->getNew();                
                break;            
            case "Contributor":
                $fieldValue = $model->contributorvalue->getNew();                
                break;     
        }
        
        $fieldValue->ComponentID = $componentID;
        $fieldValue->FieldID = $this->FieldID;
        $fieldValue->Value = $value;
        
        return $fieldValue->save();        
    }
}

