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
            $this->dropdown->addClass("dropdown-toggle");
            $this->dropdown->setAttribute("data-toggle", "dropdown");
            $this->dropdown->setAttribute("aria-haspopup", "true");
            $this->dropdown->setAttribute("aria-expanded", "false");
            
            $this->dropdown->addChild($this->caret = new tauAjaxSpan());
            $this->caret->addClass("caret");
            $this->dropdown->addChild($this->toggle = new tauAjaxSpan("Toggle Dropdown"));
            $this->toggle->addClass("sr-only");
            
            //menu 
            $this->addChild($this->menu = new tauAjaxList());
            $this->menu->addClass("dropdown-menu");                
    }
    
    public function addItem(tauAjaxXmlTag $item)
    {
        $this->menu->addItem($item);
    }            
}

?>
