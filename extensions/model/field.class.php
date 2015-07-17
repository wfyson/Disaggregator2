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
}

