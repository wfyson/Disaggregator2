<?php

class DescriptorCreator extends tauAjaxXmlTag
{

	private $descriptor;

	public function __construct(DisaggregatorPerson $person, Descriptor $descriptor=null)
	{
		parent::__construct('div');
                
                $this->person = $person;
        
                if($descriptor == null)          
                {
                    $this->descriptor = DisaggregatorModel::get()->descriptor->getNew();                            
                    $this->descriptor->UserID = $person->UserID;                    
                }
                else
                {
                    $this->descriptor = $descriptor;      
                }                    
	}

        public function init($editable=array('Name', 'Description'))
        {
            $this->setData("");
            
            $this->addChild($this->editor = new TauAjaxADRORecord(DisaggregatorModel::get()->descriptor));           
            $this->editor->attachEvent('save', $this, 'e_saved');
            $this->editor->addClass("col-md-6");
            
            $this->editor->ignore();
        
            foreach($editable as $f)
            {
                $this->editor->ignore($f, false);
            }                   
            $this->editor->show($this->descriptor);   
            
            //add field modal
            $this->editor->addChild($this->fieldModal = new BootstrapModal("add_field", "Add Field", "Add Field", "Save"));
            $this->fieldModal->addBody($this->fieldCreator = new FieldCreator());
            $this->fieldModal->attachEvent("confirm", $this, "e_addField");
            
            //styling for the editor
            $this->editor->runJS("                
                $('.tauAjaxTextInput').addClass('form-control');
                $('.tauAjaxSelect').addClass('form-control');                 
                $('button').addClass('btn btn-primary');
            ");
            
            //add a field viewer     
            $this->addChild($this->fieldViewer = new tauAjaxXmlTag('div'));
            $this->fieldViewer->addChild(new tauAjaxLabel($this->fieldList = new FieldList($this->descriptor), "Fields"));
            $this->fieldViewer->addChild($this->fieldList); 
            $this->fieldViewer->addClass("col-md-4 col-md-offset-2");
        }
        
        public function e_saved(tauAjaxEvent $e)
        {
            //we're going to need to get the fields from the descriptor fields component here!  
            $model = $this->descriptor->getModel();
            $fields = $this->fieldList->getFields();
            foreach($fields as $field)
            {
                if($field->isNew())
                {
                    $fieldID = $field->save();
                    
                    $descriptorfield = $model->descriptorfield->getNew();
                    $descriptorfield->DescriptorID = $this->descriptor->DescriptorID;
                    $descriptorfield->FieldID = $fieldID;
                    $descriptorfield->save();
                }
            }
        }
        
        public function e_addField(tauAjaxEvent $e)
        {
            $this->fieldModal->triggerEvent('close');
            
            $field = $this->fieldCreator->getField();
            $this->fieldList->addField($field);
        }
        
        public function setDescriptor(Descriptor $descriptor)
        {
            $this->descriptor = $descriptor;
        }
        
}

?>
