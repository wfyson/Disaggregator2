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
}

