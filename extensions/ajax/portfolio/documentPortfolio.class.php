<?php

class DocumentPortfolio extends DocumentList
{
    public function showDocuments(\ADROSet $documents)
    {
        if($documents->count() > 0)            
            parent::showDocuments($documents);
        else
            $this->body->addChild(new EmptyRow("documents"));
    }
    
    public function addDoc(\Document $doc)
    {
        return $this->body->addChild(new DocumentPortfolioRow($doc));
    }            
}

class DocumentPortfolioRow extends tauAjaxXmlTag
{
	private $document;

	public function __construct(Document $document)
	{
            parent::__construct("tr");

            $this->document = $document;
                  
            //name
            $this->addChild($this->cell_name = new tauAjaxXmlTag("td"));
            $this->cell_name->addChild($this->span_name = new tauAjaxSpan("$document->Name "));
            $this->span_name->addClass("h4");  
            if($document->ParentID)
            {
                $this->cell_name->addChild(new BootstrapLabel("Redacted", "danger"));
            }
            
            //download
            $this->addChild($this->cell_download = new tauAjaxXmlTag("td"));
            $this->cell_download->addChild($this->btn_download = new BootstrapLinkButton("Download", $document->getDownloadPath(), "btn-primary"));
            
	}          

}

?>
