<?php

class BootstrapCollapsible extends tauAjaxXmlTag
{
    private $id;
    
    public function __construct($id)
    {
        parent::__construct('div');
        
        $this->id = $id;
        
        $this->addClass("collapse");
        $this->setAttribute('id', $id);
    }    
              
}

class BootstrapCollapsibleLink extends tauAjaxLink
{
    public function __construct($id, $text)
    {
        parent::__construct($text, '#'. $id);
        
        $this->setAttribute('role', 'button');
        $this->setAttribute('data-toggle', 'collapse');
    }
}

class BootstrapCollapsibleButton extends BootstrapButton
{
    public function __construct($id, $text, $class=null)
    {
        parent::__construct($text, $class);
        
        $this->setAttribute('type', 'button');
        $this->setAttribute('data-toggle', 'collapse');
        $this->setAttribute('data-target', '#' . $id);
    }
}

?>
