<?php

interface FieldValue
{
    public function validate();
}

class ComponentValue extends adro implements FieldValue
{
    public function validate()
    {
        return true;
    }
}

