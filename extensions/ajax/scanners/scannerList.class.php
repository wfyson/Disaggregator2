<?php

class ScannerList extends BootstrapTable
{
    public function showScanners(ADROSet $scanners)
    {
	$this->body->setData('');		

	$i = $scanners->getIterator();
	while($i->hasNext())
	{		
		$scanner = $i->next();
		$this->addScanner($scanner);
	}
    }

    public function addScanner(Scanner $scanner)
    {
        return $this->body->addChild(new ScannerRow($scanner));
    }    
}

class ScannerRow extends tauAjaxXmlTag
{
	private $scanner;

	public function __construct(Scanner $scanner)
	{
            parent::__construct("tr");

            $this->scanner = $scanner;
                  
            //name
            $this->addChild($this->cell_name = new tauAjaxXmlTag("td"));
            $this->cell_name->addChild(new tauAjaxHeading(4, $scanner->ClassName));	                
                
            //scan button
            $this->addChild($this->cell_scan = new tauAjaxXmlTag("td"));
            $this->cell_scan->addChild($this->btn_scan = new BootstrapButton("Scan", "btn-primary"));                         
            $this->btn_scan->attachEvent("onclick", $this, "e_scan");
	}                
        
        public function e_scan(tauAjaxEvent $e)
        {
            $this->triggerEvent("run_scan", array("scanner"=>$this->scanner));
        }
        
}

?>
