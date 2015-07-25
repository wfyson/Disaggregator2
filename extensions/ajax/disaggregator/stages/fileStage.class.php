<?php

class FileStage extends BuilderStage
{
        public function __construct(Component $component, Field $field)
        {
            parent::__construct($component, $field);     
            
            //create the interface
            $this->addChild(new tauAjaxLabel($this->file_input = new TauAjaxUpload(4096, 'Add File'), "Add File: "));
            $this->addChild($this->file_input);
            $this->file_input->attachEvent('uploadcomplete', $this, "e_uploaded");                       
            
            //set or create a fieldvalue as appropriate
            if(!($this->fieldValue))
            {
                //$this->newFieldValue();
            }
            else
            {
                //$this->setValue($this->fieldValue->Value);
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
        
	    $upload = FileValue::createFromUpload($file,  $name, $this->component, $this->field);        	       
        
	    // Add the attachment as a parameter on the event and re-trigger it
	    $this->triggerEvent('uploadcomplete', array('file'=>$file, 'name'=>$name, 'upload'=>$upload));
        }
}



?>
