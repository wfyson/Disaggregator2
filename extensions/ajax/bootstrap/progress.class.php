<?php

class BootstrapProgress extends tauAjaxXmlTag
{
	public function __construct($initial = "0")
	{
		parent::__construct("div");
                $this->addClass("progress");
                
                $this->addChild($this->progressBar = new tauAjaxXmlTag('div'));
                $this->progressBar->addClass("progress-bar");
                $this->progressBar->setAttribute("role", "progressbar");
                $this->progressBar->setAttribute("aria-valuenow", $initial);
                $this->progressBar->setAttribute("aria-valuemax", "100");
                $this->progressBar->setAttribute("style", "width: $initial%");
	}
        
        public function setProgress($progress)
        {
            $this->progressBar->setAttribute("aria-valuenow", $progress);
            $this->progressBar->setAttribute("style", "width: $progress%");
        }
}


?>
