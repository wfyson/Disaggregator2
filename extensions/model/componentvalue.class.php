<?php

interface FieldValue
{
    public function validate();
    
    public function getPreview(); //returns a text description of the value
}

class ComponentValue extends adro implements FieldValue
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

