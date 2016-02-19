<?php

class Contributor extends adro 
{   
    public function getName()
    {
        return "$this->GivenName $this->FamilyName";
    }
    
    public static function getContributors()
    {
        $model = DisaggregatorModel::get();
        return $model->contributor->getRecords();
    }
    
    public static function getContributorByOrcid($orcid)
    {
        $model = DisaggregatorModel::get();

        $query = new ADROQuery($model);

        $contributors = $query->addTable($model->getTable('contributor'))->addRestriction(new adroQueryEq($query, 'contributor.Orcid', $orcid))->run();
        
        if($contributors->count() < 1)
            return false;

        return $contributors->get(0);
    }
    
    public function getPerson()
    {
        $model = DisaggregatorModel::get();
        $person = $model->person->getRecordByPK($this->UserID);
        if($person)
        {
            return $person;                    
        }
        else
        {
            return false;
        }
    }       
    
    public function requestComponents(DisaggregatorPerson $requester=null)
    {
        $model = DisaggregatorModel::get();
        $results = array();
        
        $cvs = $this->getcontributorvalues();
        $cvi = $cvs->getIterator();
        while($cvi->hasNext())
        {
            $cv = $cvi->next();
            $c = $model->component->getRecordByPK($cv->ComponentID);
            
            if($c->Security == "Public")
            {
                $results[] = $c;
            }
            elseif($c->Security == "Group" && $requester instanceof DisaggregatorPerson)
            {
                if(GroupHelper::checkGroup($c, $requester))
                {
                    $results->add($c);
                }
            }           
        }
        return $results;
    }
    
    public function getDocuments($security = false)
    {
        $person = $this->getPerson();        
        if($person)
        {
            $documents = $person->getDocuments(false, $security);
            if($documents->count() > 0)
                return $documents;
            else
                return false;
        }
        else
        {
            return false;
        }
    }      
        
    public function requestUserComponents(DisaggregatorPerson $requester=null)
    {                        
        $documents = $this->getDocuments();
        if($documents)
        {            
            $components = [];            
            $i = $documents->getIterator();
            while($i->hasNext())
            {		
		$doc = $i->next();
                $components = array_merge($components, $doc->getCompleteComponents());                                
            }
            
            $results = array();
            foreach($components as $c)
            {
                if($c->Security == "Public")
                {
                    $results[] = $c;
                }
                elseif($c->Security == "Group" && $requester instanceof DisaggregatorPerson)
                {
                    if(GroupHelper::checkGroup($c, $requester))
                    {
                        $results->add($c);
                    }
                }
            }
            return $results;
        }
    }
}

