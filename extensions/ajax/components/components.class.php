<?php

class ComponentUI extends tauAjaxXmlTag
{

	private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');

		$this->person = $person;

		$this->addChild($this->header = new BootstrapHeader("Components", "View and add components"));  
                
                $this->attachEvent('init', $this, 'e_init');
                $this->attachEvent("edit_descriptor", $this, "e_show_creator");
	}
        
        public function e_init(tauAjaxEvent $e)
        {            
            $this->addChild($this->descriptorBrowser = new DescriptorBrowser($this->person));
            
            $this->addChild($this->descriptorCreator = new DescriptorCreator($this->person));
            $this->descriptorCreator->addClass("hide");                        
        }        
        
        public function e_show_creator(tauAjaxEvent $e)
        {
            error_log("show creator");
            
            $descriptor = $e->getParam("descriptor");
            if($descriptor)
            {
                $this->descriptorCreator->setDescriptor($descriptor);
            }
            $this->descriptorCreator->init();
            $this->descriptorBrowser->addClass("hide");
            $this->descriptorCreator->removeClass("hide");
        }
        
        public function e_show_browser(tauAjaxEvent $e)
        {
            $this->descriptorCreator->addClass("hide");
            
            $this->descriptorBrowser->init();
            $this->descriptorBrowser->removeClass("hide");            
        }

}



?>
