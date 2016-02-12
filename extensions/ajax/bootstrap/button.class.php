<?php

class BootstrapButton extends tauAjaxXmlTag
{
	public function __construct($text, $class=null)
	{
            parent::__construct("button");		
            $this->addClass("btn");
            $this->setData($text);
                
            if(isset($class))
                $this->addClass($class);            
	}
}
        
class BootstrapSplitButton extends tauAjaxXmlTag
{
    public function __construct($text, $class=null)
    {        
            parent::__construct("div");		
            $this->addClass("btn-group");
            
            //first button
            $this->addChild($this->btn = new BootstrapButton($text, $class));
            
            //dropdown
            $this->addChild($this->dropdown = new BootstrapButton("", $class));
            $this->dropdown = BootstrapDropDown::createDropDown($this->dropdown);
            
            //menu 
            $this->addChild($this->menu = new tauAjaxList());
            $this->menu->addClass("dropdown-menu");                
    }
    
    public function addItem(tauAjaxXmlTag $item)
    {
        $this->menu->addItem($item);
    }            
}

class BootstrapLinkButton extends tauAjaxLink
{
    public function __construct($text, $href, $class=null)
    {
        parent::__construct($text, $href);		
        $this->addClass("btn");
              
        if(isset($class))
            $this->addClass($class);
    }
}

class BootstrapButtonGroupVertical extends tauAjaxXmlTag
{
    public function __construct()
    {    
        parent::__construct("div");
        
        $this->addClass("btn-group-vertical");
        $this->setAttribute("role", "group");
    }
    
    public function addButton(BootstrapButton $btn)
    {
        $this->addChild($btn);
    }
}

?>
