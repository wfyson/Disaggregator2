<?php

class Component extends adro 
{
    //get all the components described by the given descriptor
    public static function getComponents(Descriptor $descriptor, $complete = false)
    {
        $model = DisaggregatorModel::get();
        $query = new ADROQuery($model);
        $query->addTable($model->getTable('component'));
        $query->addRestriction(new adroQueryEq($query, 'component.DescriptorID', $descriptor->DescriptorID));
        $components = $query->run();        
        
        if($complete)
        {                        
            return self::getComplete($components);
        }
        return $components->toArray();
    }
    
    public function getFieldValues(Field $field)
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
            case "Contributor":
                $query->addTable($model->getTable('contributorvalue'));
                $query->addRestriction(new adroQueryEq($query, 'contributorvalue.ComponentID', $this->ComponentID));
                $query->addRestriction(new adroQueryEq($query, 'contributorvalue.FieldID', $field->FieldID));
                break;
        }
        $fieldValues = $query->run()->toArray();            
        
        if(count($fieldValues) > 0) // && $fieldValues->get(0)->validate())
        {
            return $fieldValues;           
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
    
    public function getDocument()
    {
        return $this->getdocuments()->get(0);
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
                if($this->getFieldValues($field) == false)
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
            $preview = $this->getFieldValues($field);
            if($preview !== false)
            {
                
                return $preview[0]->Value;
            }            
        }
        return "$descriptor->Name $this->ComponentID";
        
    }     
    
    public static function getComplete(ADROSet $components)
    {                
        $completeComponents = array();
        $i = $components->getIterator();
        while($i->hasNext())
        {
            $c = $i->next();
            if($c->isComplete() === true)
            {
                $completeComponents[] = $c;
            }                
        }
        return $completeComponents;
    }
    
    public static function getIncomplete(ADROSet $components)
    {                
        $incompleteComponents = array();
        $i = $components->getIterator();
        while($i->hasNext())
        {
            $c = $i->next();
            if($c->isComplete() !== true)
            {
                $incompleteComponents[] = $c;
            }                
        }
        return $incompleteComponents;
    }
    
    public function getContributorRoles(Contributor $contributor)
    {
        $model = DisaggregatorModel::get();
        
        $fields = $model->sqlRawQuery("SELECT * FROM field INNER JOIN contributorvalue ON contributorvalue.FieldID=field.FieldID WHERE contributorvalue.FieldID = field.FieldID  AND  (contributorvalue.Value='$contributor->ContributorID' AND contributorvalue.ComponentID='$this->ComponentID');");

        $roles = array();
        foreach($fields as $field)
        {
            $roles[] = $field['Name'];
        }
        
        return $roles;
    }
    
    public function delete()
    {
        //delete all associated values
        $textvalues = $this->gettextvalues();
        $this->deleteValues($textvalues);
        
        $componentvalues = $this->getcomponentvalues();
        $this->deleteValues($componentvalues);
                
        $filevalues = $this->getfilevalues();
        $this->deleteValues($filevalues);
        
        $contributorvalues = $this->getcontributorvalues();
        $this->deleteValues($contributorvalues);
        
        parent::delete();
    }
    
    public function deleteValues(ADROSet $values)
    {
        $iterator = $values->getIterator();
        while($iterator->hasNext())
        {
            $v = $iterator->next();
            $v->delete();
        }        
    }
    
}

