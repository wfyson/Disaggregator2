<?php

class BootstrapModal extends tauAjaxXmlTag
{
	public function __construct($id, $launch, $title, $confirm=null)
	{		
                parent::__construct('div');
            
                $this->addChild($this->launcher = new BootstrapModalLauncher($id, $launch)); 
                $this->addChild($this->modal = new BootstrapModalModal($id, $title, $confirm));               
	}        
        
        public function addBody(tauAjaxXmlTag $body)
        {
            $this->modal->addBody($this->body = $body);
        }
}

class BootstrapModalLauncher extends BootstrapButton
{
    public function __construct($id, $launch)
    {
        parent::__construct($launch, "btn-primary");        
        $this->setAttribute("data-toggle", "modal");
        $this->setAttribute("data-target", "#$id");        
    }
}

class BootstrapModalModal extends tauAjaxXmlTag
{
    public function __construct($id, $title, $confirm)
    {
        parent::__construct("div");
        
         //modal
        $this->addClass("modal fade modal-dialog");
        $this->setAttribute("id", $id);
        $this->setAttribute("tabindex", "-1");
        $this->setAttribute("role", "dialog");
                
        //modal content
	$this->addChild($this->content = new tauAjaxXmlTag('div'));
        $this->content->addClass("modal-content");
                
        //modal header
        $this->content->addChild($this->header = new BootstrapModalHeader($title));
                
        //modal body
        $this->content->addChild($this->body = new tauAjaxXmlTag('div'));
        $this->body->addClass("modal-body");
                
        //modal footer
        $this->content->addChild($this->footer = new BootstrapModalFooter($confirm));                                
    }
    
    public function addBody(tauAjaxXmlTag $body)
    {
        $this->body->addChild($body);
    }
}

class BootstrapModalHeader extends tauAjaxXmlTag
{
    public function __construct($title)
    {
        parent::__construct('div');
        
        $this->addClass("modal-header");
        
        //close button
        $this->addChild($this->close = new tauAjaxXmlTag("button"));
        $this->close->addClass("close");
        $this->close->setAttribute("data-dismiss", "modal");
        $this->close->setAttribute("aria-label", "Close");
        $this->close->addChild($this->closeSpan = new tauAjaxSpan("&times;"));
        $this->closeSpan->setAttribute("aria-hidden", "true");
        
        //title
        $this->addChild($this->title = new tauAjaxHeading(4, $title));
        $this->title->addClass("modal-title");        
    }
}

class BootstrapModalFooter extends tauAjaxXmlTag
{
    public function __construct($confirm)
    {
        parent::__construct('div');
        
        $this->addClass("modal-footer");
        
        //close button
        $this->addChild($this->close = new BootstrapButton("Close", "btn-default"));        
        $this->close->setAttribute("data-dismiss", "modal");
        
        //confirm button
        if(isset($confirm))
        {
            $this->addChild($this->confirm = new BootstrapButton($confirm, "btn-primary"));                                    
            $this->confirm->attachEvent("onclick", $this, "e_confirm");
        }
    }   
    
    public function e_confirm(tauAjaxEvent $e)
    {
        $this->triggerEvent('confirm');
    }
}

?>
