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
                    
                $this->init();
	}

        public function init($editable=array('Name', 'Description'))
        {
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
            $this->addChild($this->fieldList = new FieldList($this->descriptor)); 
            $this->fieldList->addClass("col-md-4 col-md-offset-2");
        }
        
        public function e_saved(tauAjaxEvent $e)
        {
            //we're going to need to get the fields from the descriptor fields component here!            
        }
        
        public function e_addField(tauAjaxEvent $e)
        {
            $field = $this->fieldCreator->getField();
            $this->fieldList->addField($field);
        }
        
}

class FieldList extends tauAjaxList
{
    private $descriptor;
    private $fields = array();

    public function __construct(Descriptor $descriptor)
    {
        parent::__construct();
        $this->addClass("list-group");
        
        $this->descriptor = $descriptor;
        
        //show existing fields if appropriate
    }
    
    public function addField(Field $field)
    {
        $fieldItem = new FieldListItem($field);
        $this->fields[] = $fieldItem;
        $this->addChild($fieldItem);
    }
    
}

class FieldListItem extends tauAjaxListItem
{
    private $field;
    
    public function __construct(Field $field)
    {
        parent::__construct();
        $this->addClass("list-group-item");
        
        $this->field = $field;       
        if($field->Mandatory)        
            $this->addChild(new BootstrapBadge("Req"));            
        if($field->Multi)
            $this->addChild(new BootstrapBadge("Mul"));            
        
        $this->addChild(new tauAjaxSpan($field->Name));
    }
}

class FieldCreator extends tauAjaxXmlTag
{    
    public function __construct(Field $field=null)
    {
        parent::__construct('div');
        
        if($field == null)
        {
            $this->field = DisaggregatorModel::get()->field->getNew();
        }
        else
            $this->field = $field;      
        
        $this->init();
    }    
    
    public function init($editable=array('Name', 'Type', 'Mandatory', 'Multi'))
    {        
        //name input
        $this->addChild(new tauAjaxLabel($this->txt_name = new tauAjaxTextInput(), 'Name'));
        $this->addChild($this->txt_name);        
        
        //type input
        $this->addChild($this->type = new tauAjaxXmlTag('div'));
        $this->type->addChild(new tauAjaxLabel($this->select_type = new tauAjaxSelect(), 'Type'));
        $this->type->addChild($this->select_type);
        $this->select_type->addOption("Text");
        $this->select_type->addOption("File");
        $this->select_type->addOption("Component");
        
        //$this->type->addChild(new tauAjaxLabel($this->select_component = new tauAjaxSelect(), 'Component'));
        //$this->type->addChild($this->select_component);
        //$model = Disaggregator::get();
        //$components = $model->component->getRecords();        
        //$ci = $components->getIterator();
        //while ($ci->hasNext()){
        //    $c = $ci->next();
        //    $this->select_component->addOption($c->Name, $c->ComponentID);
        //}  
        
        //mandatory
        $this->addChild(new tauAjaxLabel($this->check_mandatory = new tauAjaxCheckbox(), 'Required'));
        $this->addChild($this->check_mandatory);
        
        //multi
        $this->addChild(new tauAjaxLabel($this->check_multi = new tauAjaxCheckbox(), 'Multi'));  
        $this->addChild($this->check_multi);
    }  
    
    public function getField()
    {
        $this->field->Name = $this->txt_name->getValue();
        $this->field->Type = $this->select_type->getValue();
        $this->field->Mandatory = $this->check_mandatory->getValue();
        $this->field->Multi = $this->check_multi->getValue();
        
        return $this->field;
    }            
    
    
}



?>
