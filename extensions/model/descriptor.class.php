<?php

class Descriptor extends adro 
{
    public function deleteDescriptorFields()
    {
        $model = DisaggregatorModel::get();
        $descriptorFields = $this->getdescriptorfields();
        $i = $descriptorFields->getIterator();
        while($i->hasNext())
        {
            $df = $i->next();
            $df->delete();
        }              
    }        
    
    public function getTextFields()
    {
        $model = DisaggregatorModel::get();
        $query = new ADROQuery($model);
        $query->addTable($model->getTable('field'));
        $query->addTable($model->getTable('descriptorfield'));
        $query->addRestriction(new adroQueryEq($query, 'field.Type', 'Text'));        
	$query->addRestriction(new adroQueryEq($query, 'descriptorfield.DescriptorID', $this->DescriptorID));        
 
        return $query->run();
        
    }
    
    
    
}

