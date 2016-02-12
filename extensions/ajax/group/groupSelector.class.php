<?php

class GroupSelector extends BootstrapDropDown
{
    private $record;
    private $person;
    
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
        $this->addItem(new GroupItem("Open", "open"));
        
        //add closed option
        $this->addItem(new GroupItem("Closed", "closed"));              
        
        //add the user's options
        $gps = $this->person->getgrouppersons();
        if($gps->count() > 0)
        {
            //divider
            $this->addDivider();
            
            $gpi = $gps->getIterator();
            while($gpi->hasNext())
            {
                $gp = $gpi->next();
                $g = $model->usergroup->getRecordByPK($gp->GroupID);
                
                $this->addItem(new GroupItem($g->Name, $g->GroupID));
            }
        }
        
        //divider
        $this->addDivider();
        
        //add a new group button
        $newItem = new tauAjaxListItem();
        $newItem->addChild($this->new_btn = new BootstrapButton("New Group", "btn-sm"));
        $this->new_btn->attachEvent("onclick", $this, "e_new"); 
        $this->addItem($newItem);
    }    
    
    public function e_new(tauAjaxEvent $e)
    {
        error_log("new...");
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
        
        $this->addChild(new tauAjaxLink($name, "#"));
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        error_log("selected...." . $this->value);
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
