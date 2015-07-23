<?php

class TextStage extends BuilderStage
{  
    public function __construct (Field $field)
    {
        parent::__construct($field);
        
        $this->addChild(new tauAjaxLabel($this->txt_input = new tauAjaxTextInput(), "$field->Name: "));
        $this->addChild($this->txt_input);
    }
    
    public function isComplete()
    {                
        return true;
    }
}



?>
