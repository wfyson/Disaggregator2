<?php

class ContributorValue extends adro implements FieldValue
{
    public function validate()
    {
        return true;
    }
    
    public function getPreview()
    {
        return $this->Value;
    }
}

