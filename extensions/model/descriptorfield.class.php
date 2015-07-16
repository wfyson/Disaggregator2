<?php

class DescriptorField extends adro 
{
    public function getDisaggregatorField()
    {        
        $model = DisaggregatorModel::get();
        $field = $model->field->getRecordByPK($this->FieldID);
        return $field;
    }
}

