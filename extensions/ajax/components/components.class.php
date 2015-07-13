<?php

class ComponentUI extends tauAjaxXmlTag
{

	private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');

		$this->person = $person;

		$this->addChild($jumbo = new BootstrapJumbotron("Components"));
		$jumbo->addChild(new tauAjaxParagraph("Components describe the discrete chunks of information that may be extracted
        from a document. They can be used to describe any piece of data that may 
        provide valuable information even away from its parent document."));
	
                $this->attachEvent('init', $this, 'e_init');
	}
        
        public function e_init(tauAjaxEvent $e)
        {
            $this->addChild($this->descriptorCreator = new DescriptorCreator($this->person));
        }

}

/*
	List of components on the left hand side and a description that appears on the right
*/

class ComponentsBrowser extends tauAjaxXmlTag
{

	private $components;

	public function __construct(ADROSet $components=null)
	{
		parent::__construct('div');

		if(!$components)
		{
			$model = DisaggregatorModel();
			$query = new adroQuery($model);
        		$query->addTable($model->components);
			$this->components = $query->run();			
		}
		else
		{
			$this->components = $components;
		}

		$this->attachEvent('init', $this, 'e_init');
	}

	public function e_init(tauAjaxEvent $e)
	{

	}
}

/*
	Displays a single component
*/
class ComponentViewer extends tauAjaxXmlTag
{
	private $component;

	public function __construct()
	{
		parent::__construct('div');
	}

	public function setComponent(Component $component)
	{
		$this->component = $component;
	}
}



?>
