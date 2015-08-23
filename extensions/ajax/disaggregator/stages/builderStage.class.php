<?php

interface DisaggregatorStage
{
    public function newFieldValue();
    public function setValue($value);    
    public function storeCurrentValue();
}

abstract class BuilderStage extends tauAjaxXmlTag implements tauAjaxPage
{
    protected $component;
    protected $field;
    protected $fieldValues;
    protected $record = 0;

    public function __construct(Component $component, Field $field)
    {
	parent::__construct("div");
        $this->addClass("BuilderStage");
        
        $this->component = $component;
        $this->field = $field;
     
        $this->fieldValues = $this->component->getFieldValues($field);    
                
    }
    
    public function getFieldValues()
    {
        return $this->fieldValues;
    }
    
    public function getCurrentRecord()
    {
        return $this->fieldValues[$this->record];
    }
    
    public function isComplete()
    {
        return true;
    }
    
    public function trigger()
    {
        $this->triggerEvent('progress', array("field"=>$this->field));
    }
            
    public function addScroller()
    {
        $this->addChild($this->scroller = new BootstrapButtonGroupVertical());
        $this->scroller->addButton($this->btn_prev = new BootstrapButton("", "btn-primary btn-xs"));
        $this->scroller->addButton($this->btn_next = new BootstrapButton("", "btn-primary btn-xs"));        
        
        $this->btn_prev->addChild(new Glyphicon("triangle-top"));
        $this->btn_prev->addClass("disabled");
        $this->btn_next->addChild(new Glyphicon("triangle-bottom"));
        
        $this->btn_prev->attachEvent("onclick", $this, "e_prev");
        $this->btn_next->attachEvent("onclick", $this, "e_next");
        
        //indicator
        $this->addChild($this->indicator = new tauAjaxSpan());
        $this->indicator->addClass("indicator");
        $this->attachEvent("update_indicator", $this, "e_update_indicator");
        $this->triggerEvent("update_indicator");
    }        
    
    public function e_prev(tauAjaxEvent $e)
    {
        //store any changes to the current record
        $this->storeCurrentValue();
        
        if($this->record != 0)
        {
            $this->record--;
            if($this->record == 0)
            {
                $this->btn_prev->addClass("disabled");
            }
            $this->setValue($this->getCurrentRecord()->getPreview()); 
            $this->triggerEvent("update_indicator");
        }
    }
    
    public function e_next(tauAjaxEvent $e)
    {
        //allow user to go back
        $this->btn_prev->removeClass("disabled");        
        
        //store the record we have before creating a new one
        $this->storeCurrentValue();
        
        //next record
        $this->record++;
        if($this->getCurrentRecord() == null)
        {
            $this->fieldValues[$this->record] = $this->newFieldValue();           
        }
        $this->setValue($this->getCurrentRecord()->getPreview());   
        
        $this->triggerEvent("update_indicator");
    }
    
    public function e_update_indicator(tauAjaxEvent $e)
    {        
        $this->indicator->setData($this->record + 1 . "/" . count($this->fieldValues));
    }
        
}



?>
