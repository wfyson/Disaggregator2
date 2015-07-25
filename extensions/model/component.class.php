<?php

class Component extends adro 
{
    public function getFieldValue(Field $field)
    {
        $model = DisaggregatorModel::get();
        $query = new ADROQuery($model);
        
        switch($field->Type)
        {
            case "Text":
                $query->addTable($model->getTable('textvalue'));
                $query->addRestriction(new adroQueryEq($query, 'textvalue.ComponentID', $this->ComponentID));
                $query->addRestriction(new adroQueryEq($query, 'textvalue.FieldID', $field->FieldID));
                break;
            case "File":
                $query->addTable($model->getTable('filevalue'));
                $query->addRestriction(new adroQueryEq($query, 'filevalue.ComponentID', $this->ComponentID));
                $query->addRestriction(new adroQueryEq($query, 'filevalue.FieldID', $field->FieldID));
                break;
            case "Component":
                $query->addTable($model->getTable('componentvalue'));
                $query->addRestriction(new adroQueryEq($query, 'componentvalue.ComponentID', $this->ComponentID));
                $query->addRestriction(new adroQueryEq($query, 'componentvalue.FieldID', $field->FieldID));
                break;
        }
        $fieldValues = $query->run();
        if($fieldValues->count() > 0)
        {
            return $fieldValues->get(0);           
        }
        else
        {
            return false;
        }        
    }
}

