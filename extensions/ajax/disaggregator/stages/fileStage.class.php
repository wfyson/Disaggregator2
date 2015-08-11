<?php

class FileStage extends BuilderStage implements DisaggregatorStage
{
    public function __construct(Component $component, Field $field)
    {
        parent::__construct($component, $field);     
            
        //create the interface
        $this->addChild(new tauAjaxHeading(4, $this->field->Name));
        
        $this->addChild(new tauAjaxLabel($this->file_input = new TauAjaxUpload(4096, 'Select File', ''), "Add File: "));
        $this->addChild($this->file_input);
        $this->file_input->attachEvent('uploadcomplete', $this, "e_uploaded");    
            
        $this->addChild($this->txt_input = new tauAjaxSpan()); 
        $this->txt_input->addClass(" TauAjaxReadOnlyInput form-control");
           
        //set or create a fieldvalue as appropriate
        if(!($this->fieldValue))
        {
            $this->newFieldValue();
        }
        else
        {
            $this->setValue($this->fieldValue->Name);
        }
    }
        
    public function newFieldValue()
    {
        $model = DisaggregatorModel::get();
        $this->fieldValue = $model->filevalue->getNew();
        $this->fieldValue->ComponentID = $this->component->ComponentID;                 
        $this->fieldValue->FieldID = $this->field->FieldID;                 
    }
        
    public function setValue($value)
    {        
        $this->txt_input->setData($value);        
    }
        
    public function e_uploaded(tauAjaxEvent $e)
    {
        // We catch the event that we throw - It needs to be ignored!
	if($e->getParam('upload') !== false)
        {
            return;
        }        

        $file = $e->getParam('file');
	$name = $e->getParam('name');
        
	$e->disableBubble();
        
	$this->fieldValue = FileValue::createFromUpload($file,  $name, $this->fieldValue);        	       
        $this->setValue($this->fieldValue->Name);
        
	// Add the attachment as a parameter on the event and re-trigger it
        $this->triggerEvent('uploadcomplete', array('file'=>$file, 'name'=>$name, 'upload'=>$upload));
    }
    
    public function isComplete()
    {                                           
        //if a value has been set, save the field        
        if(($this->fieldValue->Value != "") && ($this->fieldValue->Name != ""))
        {
            $this->fieldValue->save();
        }
        return true;
    }
}



?>
