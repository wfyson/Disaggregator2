<?php

class NamespaceSelector extends tauAjaxXmlTag
{    
    private $new = false;
    
    public function __construct()
    {
        parent::__construct('div');
                                
        $this->init();
    }
    
    public function init()
    {
        //namespace select
        $this->addChild($this->select_namespace = new BootstrapSelect());
        
        $model = DisaggregatorModel::get();
        $namespaces = $model->namespace->getRecords();
        
        $i = $namespaces->getIterator();
        while($i->hasNext())
	{		
            $ns = $i->next();
            $this->select_namespace>addOption($ns->NamespaceURI);
	}
        
        //new namespace button
        $this->addChild($this->btn_new = new BootStrapButton("New Namespace", "btn-primary"));
        $this->btn_new->attachEvent("onclick", $this, "e_new_namespace");
        
        $this->attachEvent("hide_new_namespace", $this, "e_hide_new_namespace");
    }
    
    public function e_new_namespace(tauAjaxEvent $e)
    {
        if(!$this->new)
        {
            $this->addChild($this->new_ns = new NamespaceCreator());           
            $this->btn_new->addClass("disabled");     
            $this->new = true;
        }
    }    
    
    public function e_hide_new_namespace(tauAjaxEvent $e)
    {
        $this->deleteChild($this->new_ns);        
        $this->btn_new->removeClass("disabled");        
        $this->new = false;
    }
}
    
class NamespaceCreator extends tauAjaxXmlTag
{
    public function __construct()
    {
        parent::__construct('div');
                                
        $this->init();
    }
    
    public function init()
    {                
        $this->addChild($this->uri_form_group = new BootstrapFormGroup(new tauAjaxLabel($this->txt_uri = new BootstrapTextInput(), "URI"), $this->txt_uri));
        $this->txt_uri->attachEvent("ontype", $this, "e_uri");
        
        $this->addChild($this->title_form_group = new BootstrapFormGroup(new tauAjaxLabel($this->txt_title = new BootstrapTextInput(), "Title"), $this->txt_title));
                 
        $this->addChild($this->btn_save = new BootstrapButton("Save", "btn-success"));
        $this->btn_save->attachEvent("onclick", $this, "e_save");               
        
        $this->addChild($this->btn_cancel = new BootstrapButton("Cancel", "btn-danger"));
        $this->btn_cancel->attachEvent("onclick", $this, "e_cancel");   
    }
    
    public function e_uri(tauAjaxEvent $e)
    {
        //try and load the graph - if success turn box green and show title if available
        $uri = $this->txt_uri->getValue();
        $valid = LinkedDataHelper::validateNS($uri);
        if($valid)
        {            
            $this->uri_form_group->addIcon("has-success", "ok");
            
            if(is_string($valid))   
                $this->txt_title->setValue($valid);
        }        
        else   
        {
            $this->uri_form_group->removeIcon();
        }
    }
    
    public function e_save(tauAjaxEvent $e)
    {
        
    }
    
    public function e_cancel(tauAjaxEvent $e)
    {
        $this->triggerEvent("hide_new_namespace");
    }
}



?>
