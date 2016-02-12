<?php

class GroupUI extends tauAjaxXmlTag
{

	private $person;       
        
	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');

		$this->person = $person;

		$this->addChild($this->header = new BootstrapHeader("Groups", "Add and edit your groups to control the visibility of your uploads"));  
                
                $this->attachEvent('init', $this, 'e_init');
	}
        
        public function e_init(tauAjaxEvent $e)
        {
            $this->setData("");
            
            //list of groups
            $this->addChild($this->interface = new tauAjaxXmlTag('div'));
        
            //show the user's groups
            $this->interface->addChild($this->groups = new tauAjaxXmlTag('div'));
            $this->groups->addClass("col-md-5");
        
            $this->groups->addChild(new tauAjaxHeading(2, "Select a group"));
        
            $this->groups->addChild($this->groupList = new GroupListGroup());        
            $groups = $this->person->getusergroups();
            $this->groupList->showGroups($groups);
            $this->groupList->addChild(new NewGroupListGroupItem());
            $this->attachEvent('select_group', $this, 'e_select');
            
            //show the select group
            $this->interface->addChild($this->selected = new tauAjaxXmlTag('div'));
            $this->selected->addClass("col-md-5 col-md-offset-2");
            $this->selected->addChild($this->groupViewer = new GroupViewer($this->person));
            
            $this->attachEvent("new_group", $this, "e_new_group");
        }      
        
        public function e_select(tauAjaxEvent $e)
        {
            $this->groupViewer->showGroup($e->getParam("group"));
        }
        
        public function e_new_group(tauAjaxEvent $e)
        {
            $model = DisaggregatorModel::get();            
            $group = $model->usergroup->getNew();
            $group->Name = $e->getParam("name");
            $group->UserID = $this->person->UserID;
            $group->save();
            
            $this->triggerEvent("init");
        }
}

class GroupListGroup extends ListGroup
{
    
    public function showGroups(ADROSet $groups)
    {
        $this->setData('');

        $i = $groups->getIterator();
        while ($i->hasNext())
        {
            $group = $i->next();
            $this->addGroup($group);
        }
    }
    
    public function addGroup(Group $group)
    {
        $this->addChild(new GroupListGroupItem($group));
    }
    
}

class GroupListGroupItem extends ListGroupItem
{
    private $group;
    
    public function __construct(Group $group)
    {
        parent::__construct($group->Name);
        
        $this->group = $group;
        
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->runJS("$('.active').removeClass('active');");
        
        $this->addClass("active");
        $this->triggerEvent("select_group", array("group" => $this->group));                
    }
}

class NewGroupListGroupItem extends ListGroupItem
{
    public function __construct()
    {
        parent::__construct();
        
        $this->addChild(new tauAjaxLabel($this->txt_name = new BootstrapTextInput()));
        $this->addChild($this->txt_name);
        $this->txt_name->setAttribute("placeholder", "Group Name");
        
        $this->addChild($this->btn_new = new BootstrapButton("Add", "btn-success"));
        $this->btn_new->attachEvent("onclick", $this, "e_new");
    }
    
    public function e_new(tauAjaxEvent $e)
    {
        $this->triggerEvent("new_group", array("name" => $this->txt_name->getValue()));
    }
}


?>
