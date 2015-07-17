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
}

