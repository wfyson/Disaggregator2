<?php

class DescriptorCreator extends tauAjaxXmlTag
{

	private $descriptor;

	public function __construct(DisaggregatorPerson $person)
	{
            parent::__construct('div');
                
            //LinkedDataHelper::dumpNS("http://www.rsc.org/ontologies/RXNO_OWL.owl#");
            
            $this->person = $person;              
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
            
            //add namespace dropdown
            $this->editor->addChild($this->namespaceSelector = new NamespaceSelector(array("rdfs:Class", "owl:Class")));
            
            //set initial namespace selections
            $this->namespaceSelector->setNamespaceValue($this->descriptor->NamespaceID);
            $this->namespaceSelector->setSelection($this->descriptor->Class);
            
            //styling for the editor
            $this->editor->runJS("                
                $('.tauAjaxTextInput').addClass('form-control');
                $('.tauAjaxSelect').addClass('form-control');                 
                $('button').addClass('btn btn-primary');
            ");
            
            //add a field viewer     
            $this->addChild($this->fieldViewer = new tauAjaxXmlTag('div'));
            
            //preview field select
            $this->fieldViewer->addChild(new tauAjaxLabel($this->select_preview = new BootstrapSelect(), "Preview Field"));
            $this->fieldViewer->addChild($this->select_preview);            
            
            //set the options
            $textFields = $this->descriptor->getTextFields();
            $i = $textFields->getIterator();
            while ($i->hasNext())
            {
                $tf = $i->next();
                $this->select_preview->addOption($tf->Name, $tf->FieldID);
            }
            $this->select_preview->addOption("None", null);
            
            //set the initial value
            if($this->descriptor->PreviewID != null)
            {
                $this->select_preview->setValue($this->descriptor->PreviewID);
            }
            else
            {             
                $this->select_preview->setValue(null);
            }
            $this->select_preview->attachEvent("onchange", $this, "e_preview_select");          
            
            //field list
            $this->fieldViewer->addChild(new tauAjaxLabel($this->fieldList = new FieldList($this->descriptor, true), "Fields"));
            $this->fieldViewer->addChild($this->fieldList); 
            $this->fieldViewer->addClass("col-md-4 col-md-offset-2");
        }
        
        public function e_preview_select(tauAjaxEvent $e)
        {
            $this->descriptor->PreviewID = $e->getParam('value');
        }
        
        public function e_saved(tauAjaxEvent $e)
        {            
            //first delete descriptorfields
            $this->descriptor->deleteDescriptorFields();
            
            //now recreate the descriptorfields with the new list
            $model = $this->descriptor->getModel();
            $fields = $this->fieldList->getFields();
            
            foreach($fields as $field)
            {
                //create new descriptorfield entry
                $descriptorfield = $model->descriptorfield->getNew();                
                $descriptorfield->DescriptorID = $this->descriptor->DescriptorID;                                
                
                //if field is new save it before adding to descriptorfield
                if($field->isNew())
                    $field->save();
                
                $descriptorfield->FieldID = $field->FieldID;
                                
                //save the descriptor field entry
                $descriptorfield->save();
            }
                       
            //now save the namespace details
            if($this->namespaceSelector->getNamespaceValue() != null)
            {                
                $this->descriptor->NamespaceID = $this->namespaceSelector->getNamespaceValue();
                $this->descriptor->Class = $this->namespaceSelector->getSelection();
                $this->descriptor->save();
            }
            
            $name = $this->descriptor->Name;
            $alert = new BootstrapAlert("Saved component descriptor: $name", "alert-success");
            $this->triggerEvent("show_browser", array('alert'=>$alert));
        }
        
        public function e_addField(tauAjaxEvent $e)
        {
            $this->fieldModal->triggerEvent('close');
            
            $field = $this->fieldCreator->getField();
            $this->fieldList->addField($field, true);
        }
        
        public function setDescriptor(Descriptor $descriptor=null)
        {
            if(isset($descriptor))          
            {
                $this->descriptor = $descriptor;  
            }
            else
            {
                $this->descriptor = DisaggregatorModel::get()->descriptor->getNew();                            
                $this->descriptor->UserID = $this->person->UserID;
            }
        }       
}

?>