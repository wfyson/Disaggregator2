<?php

class DocumentOverviewUI extends tauAjaxXmlTag
{

    private $person;
    private $document;
    private $tab;

    public function __construct(DisaggregatorPerson $person, Document $document=null, $tab=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document; 
        $this->tab = $tab;
        
        if($tab == null)
            $this->tab = false;       

        $this->attachEvent('init', $this, 'e_init');   
        $this->attachEvent('document_select', $this, 'e_document_select');        
              
    }
        
    public function e_init(tauAjaxEvent $e)
    {      
        $this->setData("");
            
        //do we have a document?
        if(!isset($this->document))
        {
            $this->addChild($this->documentSelector = new DocumentSelector($this->person));
            return;
        }        
            
        //yes we have everything
        $this->addChild(new tauAjaxHeading(2, $this->document->Name . " disaggregated"));
        
        $this->addChild($this->tabs = new BootstrapTabs("overview"));
        
        //complete components
        $complete = new BootstrapTabPane("Complete", "complete");
        $complete->addChild(new tauAjaxHeading(3, "Components"));
        $complete->addChild(new ComponentLister($this->document->getCompleteComponents(), "complete"));
        $this->tabs->addTab($complete);
        
        //in progress components
        $progress = new BootstrapTabPane("Progress", "progress");
        $progress->addChild(new tauAjaxHeading(3, "Components in Progress"));
        $progress->addChild(new ComponentLister($this->document->getIncompleteComponents(), "progress", true));
        $this->tabs->addTab($progress);
        
        //activate given tab if appropriate  
        if($this->tab !== false)
        {
            $this->runJS("
                $('#overview a[href=\'#$this->tab\']').tab('show');
            ");
        }
        else
        {
            $this->runJS("
                console.log('we dont have a tab');
                    $('#overview a:first').tab('show');
            ");
        }       
    }             
    
    public function e_document_select(tauAjaxEvent $e)
    {
        $this->document = $e->getParam("document");
        $this->triggerEvent("init");
    }        
    
}

class ComponentLister extends tauAjaxXmlTag
{
    public function __construct($components, $id, $delete=null)
    {
        parent::__construct('div');
        
        $this->components = $components;   
        $this->id = $id;    
        $this->delete = $delete;
        
        $this->init();               
    }
    
    public function init()
    {
        $model = DisaggregatorModel::get();
        
        //sort the components in to descriptor order
        $orderedComponents = array();
        foreach($this->components as $component)
        {
            $descriptor = $component->getDescriptor();
            if(array_key_exists($descriptor->DescriptorID, $orderedComponents))
            {
                $orderedComponents[$descriptor->DescriptorID][] = $component;                
            }
            else
            {
                $orderedComponents[$descriptor->DescriptorID] = array();
                $orderedComponents[$descriptor->DescriptorID][] = $component; 
            }
        }
        ksort($orderedComponents);
        
        //now display them
        foreach($orderedComponents as $descriptorID => $components)
        {
            $descriptor = $model->descriptor->getRecordByPK($descriptorID);
            
            $this->addChild($componentTitle = new BootstrapCollapsibleLink($this->id . "_" . $descriptorID, "$descriptor->Name "));
            $componentTitle->addClass("h3 collapsed");
            $componentTitle->addChild(new Glyphicon("menu-down"));
            $componentTitle->addChild(new Glyphicon("menu-up"));
            
            $this->addChild($componentCollapse = new BootstrapCollapsible($this->id . "_" . $descriptorID));
            $componentCollapse->addChild($componentTable = new BootstrapTable());            
            foreach($components as $component)
            {
                $componentTable->body->addChild(new ComponentRow($component, $this->delete));
            }
        }
    }    
}

class ComponentRow extends tauAjaxXmlTag
{
    private $component;
    
    public function __construct(Component $component, $delete=null)
    {
        parent::__construct("tr");
                  
        $this->component = $component;
        
        //name
        $this->addChild($this->cell_name = new tauAjaxXmlTag("td"));
        $this->cell_name->addChild(new tauAjaxHeading(4, $component->getPreviewText()));	                
      
        //edit button
        $this->addChild($this->cell_disaggregator = new tauAjaxXmlTag("td"));
        $this->cell_disaggregator->addChild(new BootstrapLinkButton("Open in Disaggregator", "?f=disaggregator&component=$component->ComponentID", "btn-primary"));
    
        if($delete)
        {
            $this->addChild($this->cell_delete = new tauAjaxXmlTag("td"));
            $this->cell_delete->addChild($this->btn_delete = new BootstrapButton("", "btn-danger"));
            
            $this->btn_delete->addChild(new Glyphicon("trash"));
            $this->btn_delete->attachEvent("onclick", $this, "e_delete");
        }        
    }       
    
    public function e_delete(tauAjaxEvent $e)
    {
        $this->component->delete();
        
        //$this->triggerEvent("refresh");
    }        
}



?>
