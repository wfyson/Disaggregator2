<?php

class ListGroup extends tauAjaxList
{
	public function __construct()
	{
		parent::__construct();		
		$this->addClass("list-group");                
	}
}

class ListGroupItem extends tauAjaxListItem
{
	public function __construct($text=null)
	{
		parent::__construct();		
		$this->addClass("list-group-item");
                
                if(isset($text))
                {
                    $this->setData($text);
                }
	}
}

?>
