<?php

class Login extends tauAjaxXmlTag
{
	public function __construct()
	{
		parent::__construct('div');

		$this->addChild(new tauAjaxHeading(1, 'Yeah Yeah'));
	}
}
