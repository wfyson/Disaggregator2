<?php

class GroupViewer extends tauAjaxXmlTag        
{
    private $group;
    private $person;
    
    public function __construct(DisaggregatorPerson $person)
    {
        parent::__construct('div');
        
        $this->person = $person;
    }
    
    public function showGroup(Group $group, $saved=null)
    {
        $this->group = $group;
        $this->setData("");
        
        $this->addChild(new tauAjaxLabel($this->txt_name = new BootstrapTextInput(), "Name"));
        $this->addChild($this->txt_name);
        $this->txt_name->setValue($this->group->Name);
        
        $this->addChild($this->personList = new GroupPersonListGroup());
        $this->group->flushRelations();
        $this->personList->showGroupPersons($this->group->getgrouppersons());    
        
        $this->addChild($this->btn_save = new BootstrapButton("Save", "btn-primary"));
        $this->btn_save->attachEvent("onclick", $this, "e_save");
        
        if($saved)
        {
            $this->addChild(new BootstrapAlert("Group Saved!", "alert-success GroupSaved"));            
        }
    }    
    
    public function e_save(tauAjaxEvent $e)
    {
        //save the name
        $this->group->Name = $this->txt_name->getValue();
        $this->group->save();
        
        //save the gps
        //first delete previous gps                
        $gps = $this->group->getgrouppersons();
        $gpi = $gps->getIterator();
        while($gpi->hasNext())
        {
            $gp = $gpi->next();
            $gp->delete();
        }
        
        //now save the new ones
        $model = DisaggregatorModel::get();            
        $items = $this->personList->getItems();
        foreach($items as $item)
        {            
            if($item->getSelected())
            {
                $gp = $model->groupperson->getNew();
                $gp->GroupID = $this->group->GroupID;
                $gp->PersonID = $item->getPerson()->UserID;
                $gp->save();
            }
        }     
        $this->showGroup($this->group, true);
    }
}

class GroupPersonListGroup extends ListGroup
{
    private $items = array();
    
    public function showGroupPersons(ADROSet $grouppersons)
    {
        $this->setData('');

        $persons = DisaggregatorPerson::getUsers();        
        $gpids = $grouppersons->ArrayOf('PersonID');
        $pi = $persons->getIterator();
        while($pi->hasNext())
        {
            $p = $pi->next();
            if($p->getContributor())
            {
                $item = $this->addGroupPerson($p);              
                if(in_array($p->UserID, $gpids))
                {
                    $item->setSelected(true);
                }
            }
            
        }                 
    }
    
    public function addGroupPerson(DisaggregatorPerson $p)
    {
        $this->addChild($item = new GroupPersonListGroupItem($p));
        $this->items[$p->UserID] = $item;
        return $item;
    }
    
    public function getItems()
    {
        return $this->items;
    }
}

class GroupPersonListGroupItem extends ListGroupItem
{
    private $p;
    private $selected;
    
    public function __construct(DisaggregatorPerson $p)
    {
        
        parent::__construct($p->getContributor()->getName());
        
        $this->p = $p;    
        
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function setSelected($selected)
    {
        if($selected)
        {
            $this->selected = true;
            $this->addClass("active");
        }
        else
        {
            $this->removeClass("active");
            $this->selected = false;
        }
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        if($this->selected)
        {
            $this->setSelected(false);
        }
        else
        {
            $this->setSelected(true);
        }
    }    
    
    public function getPerson()
    {
        return $this->p;
    }
    
    public function getSelected()
    {
        return $this->selected;
    }
}


?>
