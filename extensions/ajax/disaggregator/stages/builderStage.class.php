<?php

abstract class BuilderStage extends tauAjaxXmlTag implements tauAjaxPage
{

    private $field;

    public function __construct(Field $field)
    {
	parent::__construct("div");
        $this->addClass("BuilderStage");
        
        $this->field = $field;
    }
    
    public function isComplete()
    {
        return true;
    }
    
    public function trigger()
    {
        $this->triggerEvent('progress', array("field"=>$this->field));
    }
        
}



?>
