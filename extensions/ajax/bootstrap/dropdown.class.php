<?php

class BootstrapDropDown extends tauAjaxXmlTag
{
    private $button;
    
    public function __construct(tauAjaxXmlTag $button)
    {
        parent::__construct("div");
        $this->addClass("dropdown");
        
        $button = BootstrapDropDown::createDropDown($button);
        $this->button = $button;
        
        $this->addChild($this->button);     
        
        //add menu
        $this->addChild($this->menu = new tauAjaxList());
        $this->menu->addClass("dropdown-menu");         
    }
    
    public function addItem(tauAjaxListItem $item)
    {
        $this->menu->addChild($item);
    }
    
    public function addDivider()
    {
        $divider = new tauAjaxListItem();
        $divider->setAttribute("role", "separator");
        $divider->addClass("divider");
        $this->menu->addChild($divider);
    }    
    
    public static function createDropDown(tauAjaxXmlTag $dropdown)
    {
        $dropdown->addClass("dropdown-toggle");
        $dropdown->setAttribute("data-toggle", "dropdown");
        $dropdown->setAttribute("aria-haspopup", "true");
        $dropdown->setAttribute("aria-expanded", "false");
            
        $dropdown->addChild($caret = new tauAjaxSpan());
        $caret->addClass("caret");
        $dropdown->addChild($toggle = new tauAjaxSpan("Toggle Dropdown"));
        $toggle->addClass("sr-only");
        
        return $dropdown;
    }
    
}        

?>
