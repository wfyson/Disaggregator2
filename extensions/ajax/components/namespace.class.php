<?php

class NamespaceSelector extends tauAjaxXmlTag
{    
    private $new = false;
    private $type;
    
    public function __construct($types)
    {
        parent::__construct('div');
                                
        $this->types = $types;
        
        $this->init();
    }
    
    public function init()
    {
        $this->setData("");
        
        //namespace select
        $this->addChild($this->select_namespace_form_group = new BootstrapFormGroup(new tauAjaxLabel($this->select_namespace = new BootstrapSelect(), "Namespace"), $this->select_namespace));                                 
        
        $this->select_namespace->addOption("Choose a namespace...", null);
        
        $model = DisaggregatorModel::get();
        $namespaces = $model->namespace->getRecords();       
        $i = $namespaces->getIterator();
        while($i->hasNext())
	{		
            $ns = $i->next();
            $this->select_namespace->addOption($ns->Title, $ns->NamespaceID);
	}
        $this->select_namespace->attachEvent("onchange", $this, "e_select_namespace");
        
        //URI select
        $this->addChild($this->select_uri_form_group = new BootstrapFormGroup(new tauAjaxLabel($this->select_uri = new BootstrapSelect(), "$this->type"), $this->select_uri));                                 
        
        //new namespace button
        $this->addChild($this->btn_new = new BootStrapButton("New Namespace", "btn-primary"));
        $this->btn_new->attachEvent("onclick", $this, "e_new_namespace");
        
        $this->attachEvent("hide_new_namespace", $this, "e_hide_new_namespace");
    }
    
    public function getNamespaceValue()
    {
        return $this->select_namespace->getValue();
    }
    
    public function getSelection()
    {
        return $this->select_uri->getValue();
    }
    
    public function setNamespaceValue($namespaceID)
    {        
        $this->select_namespace->setValue($namespaceID);
    }
    
    public function setSelection($selection)
    {
        //first populate with options
        $this->e_select_namespace();
        
        $this->select_uri->setValue($selection);
    }
    
    public function e_new_namespace(tauAjaxEvent $e)
    {
        if(!$this->new)
        {
            if($this->new_ns)
            {
                $this->deleteChild($this->new_ns);
            }
            
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
        
        if($e->getParam("success"))
        {
            $this->triggerEvent("refresh_namespace");
            $this->addChild($this->new_ns = new BootstrapAlert("Namespace Saved!", "alert-success NamespaceSuccess"));            
        }
    }    
    
    public function e_select_namespace()
    {
        //reinitialise select
        $this->deleteChild($this->select_uri);
        $this->addChildAtPosition($this->select_uri = new BootstrapSelect(), 1);
        
        $model = DisaggregatorModel::get();
        $namespace = $model->namespace->getRecordByPK($this->select_namespace->getValue());
        
        $results = LinkedDataHelper::getNamespaceTypes($namespace->NamespaceURI, $this->types);
        
        if(count($results) > 0)
        {
            foreach($results as $resource => $label)
            {
                if($label == "")
                {
                    $this->select_uri->addOption($resource);
                }            
                else
                {
                    $this->select_uri->addOption($label, $resource);
                }
            }
        }
        else
        {
            $this->select_uri->addOption("No results...", NULL);
        }
    }
}
    
class NamespaceCreator extends tauAjaxXmlTag
{
    private $validNamespace = false;
    
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
            $this->validNamespace = true;
            if(is_string($valid) && $valid != "[NULL]")   
                $this->txt_title->setValue($valid);
        }        
        else   
        {
            $this->validNamespace = false;
            $this->uri_form_group->removeIcon();
        }
    }
    
    public function e_save(tauAjaxEvent $e)
    {
        $model = DisaggregatorModel::get();
        $namespace = $model->namespace->getNew();
            
        if(!$this->validNamespace)
        {
            $this->uri_form_group->addIcon("has-warning", "warning-sign");
            return;
        }
        if($this->txt_title->getValue() == "")
        {
            $this->title_form_group->addIcon("has-warning", "warning-sign");
            return;
        }
        
        $namespace->NamespaceURI = $this->txt_uri->getValue();
        $namespace->Title = $this->txt_title->getValue();        
        $namespace->save(); 
        
        $this->triggerEvent("hide_new_namespace", array("success" => true));
    }
    
    public function e_cancel(tauAjaxEvent $e)
    {
        $this->triggerEvent("hide_new_namespace");
    }
}



?>
