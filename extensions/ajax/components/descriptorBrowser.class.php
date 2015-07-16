<?php

class DescriptorBrowser extends tauAjaxXmlTag
{
        private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');
                
                $this->person = $person;

		$this->addChild(new tauAjaxHeading(2, 'Component Descriptors'));
			
		$this->init();
	}

        public function init()
        {                              
            $this->addChild($this->descriptorList = new DescriptorList());
            $this->descriptorList->addClass("col-md-4");    
                
            $model = DisaggregatorModel::get();
            $descriptors = $model->descriptor->getRecords();        
            $this->descriptorList->showDescriptors($descriptors);
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
        $this->addClass("active");
    }
}



?>
