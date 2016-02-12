<?php

class GroupSelector extends BootstrapDropDown
{
    private $record;
    private $person;
    private $selected;
    private $items = array();
    
    public function __construct($record, DisaggregatorPerson $person)
    {                
        parent::__construct(new Glyphicon("eye-open"));
        
        $this->record = $record;
        $this->person = $person;
        
        $this->init();
    }
    
    public function init()
    {        
        $model = DisaggregatorModel::get();
        
        //add the open to all option
        $this->addItem($open = new GroupItem("Open", "Public"));
        
        //add closed option
        $this->addItem($closed = new GroupItem("Closed", "User"));                                      
        
        //add the user's options
        $groups = $this->person->getusergroups();
        if($groups->count() > 0)
        {
            //divider
            $this->addDivider();
            
            $gi = $groups->getIterator();
            while($gi->hasNext())
            {
                $g = $gi->next();
                
                $this->addItem(new GroupItem($g->Name, $g->GroupID));
            }
        }      
        
        //selection event
        $this->attachEvent("selection", $this, "e_selection");
        
        $this->setSelected();
    }    
    
    public function addItem(\tauAjaxListItem $item)
    {
        parent::addItem($item);
        $this->items[] = $item;
    }    
    
    public function e_selection(tauAjaxEvent $e)
    {
        $e->disableBubble();
        
        $this->selected->removeTick();        
        $this->selected = $e->getNode();

        if($e->getParam('value') == "Public" || $e->getParam('value') == "User")               
        {
            $this->record->Security = $e->getParam('value');
            $this->record->GroupID = 0;
        }
        else
        {
            $this->record->Security = "Group";
            $this->record->GroupID = $e->getParam('value');
        }
        $this->record->save();
    }
    
    public function setSelected()
    {  
        foreach($this->items as $item)
        {     
            if($item->getValue() == $this->record->Security)
            {
                $this->selected = $item;
                $item->addTick();
            }
            elseif($item->getValue() == $this->record->GroupID)
            {
                $this->selected = $item;
                $item->addTick();
            }
       }
    }
}
        
class GroupItem extends tauAjaxListItem
{
    private $name;
    private $value;
    
    public function __construct($name, $value)
    {
        parent::__construct();
                
        $this->name = $name;
        $this->value = $value;
        
        $this->addChild($this->link = new tauAjaxLink($name, "#"));
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->addTick();
        $this->triggerEvent("selection", array("value" => $this->value));
    }
    
    public function addTick()
    {
        $this->link->addChild($this->tick = new Glyphicon("ok"));
    }
    
    public function removeTick()
    {
        if($this->tick)
            $this->link->deleteChild($this->tick);
    }
    
    public function getValue()
    {
        return $this->value;
    }
}

class AdroGroupItem extends GroupItem
{
    public function __construct(Group $group)
    {
        parent::__construct($group->Name, $group->GroupID);
    }
}

?>
