<?php

class Component extends adro 
{
    //get all the components described by the given descriptor
    public static function getComponents(Descriptor $descriptor)
    {
        $model = DisaggregatorModel::get();
        $query = new ADROQuery($model);
        $query->addTable($model->getTable('component'));
        $query->addRestriction(new adroQueryEq($query, 'component.DescriptorID', $descriptor->DescriptorID));
        return $query->run();        
    }
    
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
        
        $count = $fieldValues->count();
                
        if($fieldValues->count() > 0)
        {
            return $fieldValues->get(0);           
        }
        else
        {        
            return false;
        }        
    }
    
    public function getDescriptor()
    {
        return $this->getdescriptors()->get(0);        
    }
    
    public function isComplete()
    {
        $missing = array();
        
        $descriptor = $this->getDescriptor();
        
        $descriptorFields = $descriptor->getdescriptorfields();
        $i = $descriptorFields->getIterator();
        while($i->hasNext())
        {
            $descriptorField = $i->next();
            $field = $descriptorField->getDisaggregatorField();            
            if($field->Mandatory)
            {                
                if($this->getFieldValue($field) == false)
                {
                    $missing[] = $field->Name;
                }
            }
        }        
        if(count($missing) == 0)
        {            
            return true;
        }
        else
        {            
            return $missing;
        }
    }
    
    public function getPreviewText()
    {
        $descriptor = $this->getDescriptor();
        
        if($descriptor->PreviewID != null)
        {
            $model = DisaggregatorModel::get();
            $field = $model->field->getRecordByPK($descriptor->PreviewID);
            $preview = $this->getFieldValue($field);
            if($preview !== false)
            {
                return $preview->Value;
            }            
        }
        return "$descriptor->Name $this->ComponentID";
        
    }        
}

