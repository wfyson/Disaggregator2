<?php

class DocumentOverviewUI extends tauAjaxXmlTag
{

    private $person;
    private $document;
    private $tab;

    private $helpText = "
        <p>The 'Complete' tab shows those components where all requried information has been added and will show on your portfolio if set to do so.</p>
        <p>The 'Progress' tab shows components which still require some extra information. THese can be reopened in the document disaggregaation interface.</p>
        <p>To control which components should appear in your portfolio, click the red padlock or green eye open to set visibility as private or public respectively.</p>
    ";
    
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
        $this->attachEvent('refresh', $this, 'e_refresh');
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
        $this->document->flushRelations();    
        
        //yes we have everything
        $this->addChild($heading = new BootstrapHeader($this->document->Name . " disaggregated"));
        HelperUtil::addHelpGlyph($heading->getHeader(), "bottom", $this->helpText);
        HelperUtil::initHelpGlyph($this);
        
        $this->addChild($this->tabs = new BootstrapTabs("overview"));
        
        //complete components
        $complete = new BootstrapTabPane("Complete", "complete");
        $complete->addChild(new tauAjaxHeading(3, "Components"));
        $complete->addChild($this->completeLister = new ComponentLister("complete", $this->person));
        $this->tabs->addTab($complete);
        
        //in progress components
        $progress = new BootstrapTabPane("Progress", "progress");
        $progress->addChild(new tauAjaxHeading(3, "Components in Progress"));
        $progress->addChild($this->incompleteLister = new ComponentLister("progress", $this->person, true));
        $this->tabs->addTab($progress);
        
        //activate given tab if appropriate  
        if($this->tab !== false)
        {
            $this->tab = substr($this->tab, 0, -1); //remove trailing slash           
            $this->runJS("
                $('#overview a[href=\'#$this->tab\']').tab('show');
            ");
        }
        else
        {
            $this->runJS("
                    $('#overview a:first').tab('show');
            ");
        }     
        
        $this->triggerEvent('refresh');
    }     
    
    public function e_refresh(tauAjaxEvent $e)
    {
        $this->document->flushRelations();	
	$this->completeLister->showComponents($this->document->getCompleteComponents());        
        $this->incompleteLister->showComponents($this->document->getIncompleteComponents());        
    }
    
    public function e_document_select(tauAjaxEvent $e)
    {
        $this->document = $e->getParam("document");
        $this->triggerEvent("init");
    }        
    
}

class ComponentLister extends tauAjaxXmlTag
{
    private $person;
    
    public function __construct($id, DisaggregatorPerson $person=null, $delete=null)
    {
        parent::__construct('div');
         
        $this->id = $id;    
        $this->delete = $delete;
        $this->person = $person;
        
        $this->attachEvent("remove", $this, "e_remove");
    }
    
    public function showComponents($components)
    {
        $this->setData("");
        
        $model = DisaggregatorModel::get();
        
        //sort the components in to descriptor order
        $orderedComponents = array();
        foreach($components as $component)
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
                $componentTable->body->addChild(new ComponentRow($this->person, $component, $this->delete));
            }
        }
    }
    
    public function e_remove(tauAjaxEvent $e)
    {
        $this->deleteChild($e->getParam('component'));
    }
}

class ComponentRow extends tauAjaxXmlTag
{
    private $component;
    private $person;
    
    public function __construct(DisaggregatorPerson $person, Component $component, $delete=null)
    {
        parent::__construct("tr");
                  
        $this->person = $person;
        $this->component = $component;        
        
        $this->init($delete);
    }
    
    public function init($delete)
    {        
        $this->setData();
        
        //name
        $this->addChild($this->cell_name = new tauAjaxXmlTag("td"));        
        $this->cell_name->addChild($name = new tauAjaxHeading(4, $this->component->getPreviewText() . " "));
        if($this->component->Source == "scanner")
        {
            $name->addChild(new BootstrapLabel("scanner", "info"));
        }
        $this->cell_name->addChild($this->security = new GroupSelector($this->component, $this->person));            
      
        //edit button
        $this->addChild($this->cell_disaggregator = new tauAjaxXmlTag("td"));
        $this->cell_disaggregator->addChild(new BootstrapLinkButton("Open in Disaggregator", "/disaggregator&component=" . $this->component->ComponentID, "btn-primary"));
    
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
        
        $this->triggerEvent("remove", array("component"=>$this));
    }     
}



?>
