<?php

abstract class BuilderStage extends tauAjaxXmlTag implements tauAjaxPage
{

    private $field;

    public function __construct(Field $field)
    {
	parent::__construct();
        
        $this->field = $field;
    }
    
    public function isComplete()
    {
        
    }
    
    public function trigger()
    {
        
    }
}



?>
