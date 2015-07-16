<?php

class FieldList extends ListGroup
{
    private $descriptor;
    private $fields = array();

    public function __construct(Descriptor $descriptor)
    {
        parent::__construct();
        
        $this->descriptor = $descriptor;                
        
        //show existing fields if appropriate
        if($descriptor->isNew() || count($descriptor->getdescriptorfields()) == 0)
        {
            $this->addChild(new ListGroupItem("No Fields"));
        }
        else
        {
            //show fields            
            $descriptorFields = $this->descriptor->getdescriptorfields();
            $i = $descriptorFields->getIterator();
            while($i->hasNext())
            {
                $descriptorField = $i->next();
                $field = $descriptorField->getDisaggregatorField();
                $this->addField($field);
            }
        }
    }
    
    public function addField(Field $field)
    {                        
        //remove "No Fields" item if present
        if(count($this->fields) == 0)
        {
            $this->setData("");
        }
        
        //add new field item
        $fieldItem = new FieldListItem($field);
        $this->fields[] = $fieldItem;
        $this->addChild($fieldItem);
    }       
    
    public function getFields()
    {
        $returnFields = array();
        foreach($this->fields as $fieldItem)
        {
            $returnFields[] = $fieldItem->getField();
        }
        return $returnFields;
    }
}

class FieldListItem extends ListGroupItem
{
    private $field;
    
    public function __construct(Field $field)
    {
        parent::__construct();
        
        $this->field = $field;       
        if($field->Mandatory)        
            $this->addChild(new BootstrapBadge("Req"));            
        if($field->Multi)
            $this->addChild(new BootstrapBadge("Mul"));            
        
        $this->addChild(new tauAjaxSpan($field->Name));
    }
    
    public function getField()
    {
        return $this->field;
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
        $this->addChild($this->mandatory = new tauAjaxXmlTag('div'));
        $this->mandatory->addChild(new tauAjaxLabel($this->check_mandatory = new tauAjaxCheckbox(), 'Required'));
        $this->mandatory->addChild($this->check_mandatory);
        
        //multi
        $this->addChild($this->multi = new tauAjaxXmlTag('div'));
        $this->multi->addChild(new tauAjaxLabel($this->check_multi = new tauAjaxCheckbox(), 'Multi'));  
        $this->multi->addChild($this->check_multi);
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
