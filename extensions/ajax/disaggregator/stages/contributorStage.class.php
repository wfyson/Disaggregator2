<?php

class ContributorStage extends BuilderStage implements DisaggregatorStage
{    
    public function __construct(Component $component, Field $field)
    {     
        parent::__construct($component, $field);
        
        $this->addChild(new tauAjaxHeading(4, $this->field->Name));
        $this->addChild(new tauAjaxLabel($this->contributor_input = new tauAjaxSpan(), "Select a contributor:"));
        $this->addChild($this->contributor_input);
        $this->contributor_input->addClass("TauAjaxReadOnlyInput form-control");
        
        $this->addChild($this->btn_delete = new BootstrapButton("", "btn-danger"));
        $this->btn_delete->addChild(new Glyphicon("trash"));
        $this->btn_delete->attachEvent("onclick", $this, "e_delete");
        
        if($field->Multi)
        {
            $this->addScroller();
        }  
        
        //set or create a fieldvalue as appropriate
        if(!($this->fieldValues[$this->record]))
        {
            $this->fieldValues = array();
            $this->fieldValues[$this->record] = $this->newFieldValue();
        }
        else
        {
            $this->setValue($this->fieldValues[$this->record]->Value);
        }
    }
    
    public function newFieldValue()
    {
        $model = DisaggregatorModel::get();
        $fieldValue = $model->contributorvalue->getNew();
        $fieldValue->ComponentID = $this->component->ComponentID;                 
        $fieldValue->FieldID = $this->field->FieldID;                 
        
        return $fieldValue;
    }
    
    public function setValue($value)
    {        
        $model = DisaggregatorModel::get();
        
        if($value)
        {
            $component = $model->contributor->getRecordByPK($value);
            $this->contributor_input->setData($component->getName());
        }
        else
        {
            $this->contributor_input->setData("");
        }
        
        $this->fieldValues[$this->record]->Value = $value; 
    }
    
    public function storeCurrentValue()
    {
        //do nothing?
    }
    
    public function e_delete(tauAjaxEvent $e)
    {
        $fieldValue = $this->getCurrentRecord();
        $fieldValue->delete();
        
        $this->fieldValues[$this->record] = $this->newFieldValue();
        $this->contributor_input->setData("");
    }

    public function trigger()
    {
        parent::trigger();
        
        //hide document viewer and show a list of relevant contirbutors instead        
        $this->triggerEvent("show_contributors");                    
    }
    
    public function isComplete()
    {                                              
        //save all of the recorded fields 
        foreach($this->fieldValues as $record => $fieldValue)
        {                       
            if($fieldValue->Value)
            {
                $fieldValue->save();
            }
        }                         
        return true;
    }
}



?>
