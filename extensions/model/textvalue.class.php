<?php

class TextValue extends adro implements FieldValue 
{
    public function validate()
    {
        if($this->Value == "")
        {
            return false;           
        }
        else
        {
            return true;
        }
    }
    
    public function getPreview()
    {
        return $this->Value;
    }
}

