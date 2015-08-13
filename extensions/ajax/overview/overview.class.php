<?php

class DocumentOverviewUI extends tauAjaxXmlTag
{

    private $person;
    private $document;

    public function __construct(DisaggregatorPerson $person, Document $document=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document;

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
        $progress->addChild(new ComponentLister($this->document->getIncompleteComponents(), "progress"));
        $this->tabs->addTab($progress);
        
        //activate frist tab by default
        $this->runJS("
            $('#overview a:first').tab('show')
        ");        
    }             
    
    public function e_document_select(tauAjaxEvent $e)
    {
        $this->document = $e->getParam("document");
        $this->triggerEvent("init");
    }        
    
}

class ComponentLister extends tauAjaxXmlTag
{
    public function __construct($components, $id)
    {
        parent::__construct('div');
        
        $this->components = $components;   
        $this->id = $id;
        
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
            
            $this->addChild($componentTitle = new BootstrapCollapsibleLink($this->id . "_" . $descriptorID, $descriptor->Name));
            $componentTitle->addClass("h3");
            
            $this->addChild($componentCollapse = new BootstrapCollapsible($this->id . "_" . $descriptorID));
            $componentCollapse->addChild($componentTable = new BootstrapTable());            
            foreach($components as $component)
            {
                $componentTable->body->addChild(new ComponentRow($component));
            }
        }
    }
}

class ComponentRow extends tauAjaxXmlTag
{
    public function __construct(Component $component)
    {
        parent::__construct("tr");
                  
        //name
        $this->addChild($this->cell_name = new tauAjaxXmlTag("td"));
        $this->cell_name->addChild(new tauAjaxHeading(4, $component->getPreviewText()));	                
      
        //edit button
        $this->addChild($this->cell_disaggregator = new tauAjaxXmlTag("td"));
        $this->cell_disaggregator->addChild(new BootstrapLinkButton("Open in Disaggregator", "?f=disaggregator&component=$component->ComponentID", "btn-primary"));
    }                                
}



?>
