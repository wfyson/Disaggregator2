<?php

class BootstrapTabs extends tauAjaxXmlTag
{
    private $id;
    
    public function __construct($id)
    {
        parent::__construct('div');
        
        $this->id = $id;
        
        $this->addChild($this->tabList = new BootstrapTabList($id));
        
        $this->addChild($this->tabContent = new tauAjaxXmlTag('div'));
        $this->tabContent->addClass('tab-content');
    }
    
    public function addTab(BootstrapTabPane $tabPane)
    {
        $this->tabContent->addChild($tabPane);
        $this->tabList->addChild($tab = new BootstrapTab($tabPane->getTitle(), $tabPane->getID())); 
    }
}

class BootstrapTabList extends tauAjaxList
{
    public function __construct($id)
    {
        parent::__construct();
                
        $this->addClass("nav nav-tabs");
        $this->setAttribute("id", $id);
        $this->setAttribute("role", "tablist");
    }                
}

class BootstrapTab extends tauAjaxListItem
{
    public function __construct($title, $id)
    {
        parent::__construct();  
        
        $this->setAttribute("role", "presentation");
        
        $this->addChild($this->link = new tauAjaxLink($title, "#$id"));
        $this->link->setAttribute("aria-controls", $id);
        $this->link->setAttribute("role", "tab");
        $this->link->setAttribute("data-toggle", "tab");
    }
}

    
class BootstrapTabPane extends tauAjaxXmlTag
{
    private $title;
    private $id;
    
    public function __construct($title, $id)
    {
        parent::__construct('div');
        
        $this->title = $title;
        $this->id = $id;
        
        $this->addClass("tab-pane");
        
        $this->setAttribute("id", $id);
        $this->setAttribute("role", "tabpanel");
    }
    
    public function getTitle()
    {
        return $this->title;        
    }
    
    public function getID()
    {
        return $this->id;        
    }
}


?>
