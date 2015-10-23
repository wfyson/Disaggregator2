<?php

class DescriptorBrowser extends tauAjaxXmlTag
{
        private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');
                
                $this->person = $person;
			
		$this->init();
	}

        public function init(BootstrapAlert $alert=null)
        {   
            $this->setData("");
            
            if(isset($alert))
            {
                $this->addChild($alert);
            }
            
            //show the descriptors
            $this->addChild($this->descriptors = new tauAjaxXmlTag('div'));
            $this->descriptors->addClass("col-md-5"); 
            $this->descriptors->addChild(new tauAjaxHeading(2, 'Component Descriptors'));
            $this->descriptors->addChild($this->descriptorList = new DescriptorList());
                                           
            $model = DisaggregatorModel::get();
            $descriptors = $model->descriptor->getRecords();        
            $this->descriptorList->showDescriptors($descriptors);
            
            //button for new descriptors
            $this->descriptors->addChild($this->btn_component = new BootstrapButton("Add New Component", "btn-success"));
            $this->btn_component->attachEvent("onclick", $this, "e_add_component");
            
            //add a descriptor viewer for more detail
            $this->addChild($this->descriptorViewer = new DescriptorViewer($this->person));
            $this->descriptorViewer->addClass("col-md-5 col-md-offset-2");
            $this->attachEvent("show_descriptor", $this, "e_show_descriptor");
        }
        
        public function e_show_descriptor(tauAjaxEvent $e)
        {
            $this->descriptorViewer->showDescriptor($e->getParam('descriptor'));
        }
        
        public function e_add_component(tauAjaxEvent $e)
        {
            $this->triggerEvent("edit_descriptor");
        }
}

class DescriptorList extends ListGroup
{
	public function showDescriptors(ADROSet $descriptors)
	{
		$this->setData('');		

		$i = $descriptors->getIterator();
		while($i->hasNext())
		{		
			$descriptor = $i->next();
			$this->addDescriptor($descriptor);
		}
	}	
        
        public function addDescriptor($descriptor)
        {
            $this->addChild(new DescriptorListItem($descriptor));
        }
}

class DescriptorListItem extends ListGroupItem
{
    private $descriptor;
    
    public function __construct(Descriptor $descriptor)
    {
        parent::__construct($descriptor->Name);
        
        $this->descriptor = $descriptor;
        
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->runJS("$('.active').removeClass('active');");
        
        $this->addClass("active");
        $this->triggerEvent("show_descriptor", array("descriptor" => $this->descriptor));                
    }
}

class DescriptorViewer extends tauAjaxXmlTag        
{
    private $descriptor;
    private $person;
    
    public function __construct(DisaggregatorPerson $person)
    {
        parent::__construct('div');
        
        $this->person = $person;
    }
    
    public function showDescriptor(Descriptor $descriptor, $readonly=array('Name', 'Description'))
    {
        $model = DisaggregatorModel::get();
        
        $this->descriptor = $descriptor;
        $this->setData("");
        
        $this->addChild($this->record = new TauAjaxADRORecord(DisaggregatorModel::get()->descriptor));           
        
        $this->record->ignore();
        foreach($readonly as $f)
        {
            $this->record->ignore($f, false);
            $this->record->readonly($f, true);
        }
        
        $this->record->show($this->descriptor);
        
        //styling for the record
        $this->record->runJS("                
            $('.TauAjaxReadOnlyInput').addClass('form-control');
        ");
        
        //add the fields
        $this->addChild(new tauAjaxLabel($this->fieldList = new FieldList($this->descriptor), "Fields"));
        $this->addChild($this->fieldList);
        
        //add edit button if appropriate
        if($this->descriptor->UserID == $this->person->UserID)
        {
            $this->addChild($this->btn_edit = new BootstrapButton("Edit", "btn-primary"));
            $this->btn_edit->attachEvent("onclick", $this, "e_edit");
        }
        else
        {
            $creator = $model->person->getRecordByPK($descriptor->UserID);
	    $contributor = $creator->getContributor();
	    $link = new tauAjaxLink($contributor->getName(), "/?f=portfolio&contributor=$contributor->ContributorID");
            $this->addChild($linkSpan = new tauAjaxSpan("Created by "));
	    $linkSpan->addChild($link);           
        }
    }
    
    public function e_edit(tauAjaxEvent $e)
    {
       $this->triggerEvent("edit_descriptor", array('descriptor' => $this->descriptor)); 
    }
}



?>
