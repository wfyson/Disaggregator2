<?php

class Scanner extends adro 
{
    public function runScan(Document $document)
    {
        $scanner = new $this->ClassName();
                        
        $results = $scanner::read($document);
                
        return $results;
    }    
    
    public function getDescriptor()
    {
        return $this->getdescriptors()->get(0);        
    }
    
    public static function getScannerByClassName($name)
    {        			
        $model = DisaggregatorModel::get();
                
	$query = new ADROQuery($model);
		
	$scanners = $query->addTable($model->getTable('scanner'))->addRestriction(new adroQueryEq($query, 'scanner.ClassName', $name))->run();
			
    	return $scanners->get(0);
    }
}

