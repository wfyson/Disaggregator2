<?php

interface DisaggregatorStage
{
    public function newFieldValue();
    public function setValue($value);    
}

abstract class BuilderStage extends tauAjaxXmlTag implements tauAjaxPage
{
    protected $component;
    protected $field;
    protected $fieldValue;

    public function __construct(Component $component, Field $field)
    {
	parent::__construct("div");
        $this->addClass("BuilderStage");
        
        $this->component = $component;
        $this->field = $field;
     
        $this->fieldValue = $this->component->getFieldValue($field);        
    }
    
    public function getFieldValue()
    {
        return $this->fieldValue;
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
