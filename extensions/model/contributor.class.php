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
    
    public function getDocuments($security = "Public")
    {
        $model = DisaggregatorModel::get();
        $person = $model->person->getRecordByPK($this->UserID);
        
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
    
    public function getComponents()
    {
        $model = DisaggregatorModel::get();
        $results = array();
        
        $cvs = $this->getcontributorvalues();
        $cvi = $cvs->getIterator();
        while($cvi->hasNext())
        {
            $cv = $cvi->next();
            $component = $model->component->getRecordByPK($cv->ComponentID);
            $results[] = $component;
        }
        return $results;
    }

}

