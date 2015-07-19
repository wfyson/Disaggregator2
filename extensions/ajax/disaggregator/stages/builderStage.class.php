<?php

abstract class BuilderStage extends tauAjaxXmlTag implements tauAjaxPage
{

    private $field;

    public function __construct(Field $field)
    {
	parent::__construct("div");
        
        $this->field = $field;
        
        $this->addChild(new tauAjaxHeading(4, $field->Name));
    }
    
    public function isComplete()
    {
        return true;
    }
    
    public function trigger()
    {
        
    }
}



?>
