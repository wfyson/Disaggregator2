<?php

class FileStage extends BuilderStage implements DisaggregatorStage
{
    private $value;
    
    private $helpText = "
        <p>Upload a file to associate with this component.</p>
    ";
    
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
            $this->setValue($this->fieldValues[$this->record]->getPreview());
        }
        
        HelperUtil::addHelpGlyph($this, "right", $this->helpText);
        HelperUtil::initHelpGlyph($this);
    }
        
    public function newFieldValue()
    {
        $model = DisaggregatorModel::get();
        $fieldValue = $model->filevalue->getNew();
        $fieldValue->ComponentID = $this->component->ComponentID;                 
        $fieldValue->FieldID = $this->field->FieldID;                 
        
        return $fieldValue;
    }
        
    public function setValue($value)
    {                    
        $this->txt_input->setData($value);     
        $this->value = $value;
    }
    
    public function storeCurrentValue()
    {    
        $root = $_SERVER['DOCUMENT_ROOT'];
        $fileDir = "sites/Disaggregator2/data/files/";
        if(file_exists($root . $fileDir . $value))
        {
            $this->fieldValues[$this->record]->Name = $this->value;
            $this->fieldValues[$this->record]->Value = $this->value;            
        }
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
        
	$this->fieldValue = FileValue::createFromUpload($file,  $name, $this->fieldValues[$this->record]);        	       
        $this->setValue($this->fieldValue->Name);
        
	// Add the attachment as a parameter on the event and re-trigger it
        $this->triggerEvent('uploadcomplete', array('file'=>$file, 'name'=>$name, 'upload'=>$upload));
    }
    
    public function e_delete()
    {
        $fieldValue = $this->getCurrentRecord();
        $fieldValue->delete();
        
        $this->fieldValues[$this->record] = $this->newFieldValue();
        
        $this->setValue("");
    }
    
    public function isComplete()
    {                                           
        //save all of the recorded fields 
        foreach($this->fieldValues as $record => $fieldValue)
        {
            //if a value has been set, save the field        
            if(($fieldValue->Value != "") && ($fieldValue->Name != ""))
            {
                $fieldValue->save();
            }
        }
        return true;
    }
}



?>
