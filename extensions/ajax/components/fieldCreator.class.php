<?php

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
        $this->select_type->attachEvent("onchange", $this, "e_select_type");
        
        $this->type->addChild($this->subtype = new tauAjaxXmlTag('div'));
        $this->subtype->addClass("hide");
        $this->subtype->addChild(new tauAjaxLabel($this->select_descriptor = new tauAjaxSelect(), 'Component'));
        $this->subtype->addChild($this->select_descriptor);
        
        $model = DisaggregatorModel::get();
        $descriptors = $model->descriptor->getRecords();        
        $i = $descriptors->getIterator();
        while ($i->hasNext()){
            $d = $i->next();
            $this->select_descriptor->addOption($d->Name, $d->DescriptorID);
        }  
        
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
        
        if($this->field->Type == "Component")
        {
            $this->field->DescriptorType = $this->select_descriptor->getValue();
        }
        
        return $this->field;
    }            
    
    public function e_select_type(tauAjaxEvent $e)
    {
        if($this->select_type->getValue() == "Component")
        {
            $this->subtype->removeClass("hide");
        }
        else
        {
            $this->subtype->addClass("hide");
        }
    }
}

class FieldList extends ListGroup
{
    private $descriptor;
    private $fields = array();

    public function __construct(Descriptor $descriptor, $editable=false)
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
                $this->addField($field, $editable);
            }
        }
        
        $this->attachEvent("delete_field", $this, "e_delete_field");
    }
    
    public function addField(Field $field, $editable=false)
    {                        
        //remove "No Fields" item if present
        if(count($this->fields) == 0)
        {
            $this->setData("");
        }
        
        //add new field item
        $fieldItem = new FieldListItem($field, $editable);
        $this->fields[] = $fieldItem;
        $this->addChild($fieldItem);
    }       
    
    public function removeField(FieldListItem $item)
    {        
        $newItems = array();
        foreach($this->fields as $fieldItem)
        {
            if($fieldItem != $item)
                $newItems[] = $fieldItem;                
        }
        $this->fields = $newItems;
        
        $this->deleteChild($item);
        
        if(count($this->fields) == 0)
        {
            $this->addChild(new ListGroupItem("No Fields"));
        }
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
    
    public function e_delete_field(tauAjaxEvent $e)
    {
        $this->removeField($e->getParam("field"));
    }
}

class FieldListItem extends tauAjaxListItem
{
    private $field;
    
    public function __construct(Field $field, $editable=false)
    {
        parent::__construct();
        
        $this->field = $field;   
        $this->addChild($this->details = new ListGroupItem());
        if($field->Mandatory)        
            $this->details->addChild(new BootstrapBadge("Req"));            
        if($field->Multi)
            $this->details->addChild(new BootstrapBadge("Mul"));            
        
        $type = $field->getTypeName();        
        $this->details->addChild(new tauAjaxSpan("$field->Name ($type)"));
        
        //delete button
        if($editable)
        {
            $this->addChild($this->btn_delete = new BootstrapButton("", "btn-danger"));
            $this->btn_delete->addChild(new Glyphicon("trash"));
            $this->btn_delete->attachEvent("onclick", $this, "e_delete");
        }
    }
    
    public function getField()
    {
        return $this->field;
    }
    
    public function e_delete(tauAjaxEvent $e)
    {
        $this->triggerEvent("delete_field", array("field" => $this));
    }            
}

?>
