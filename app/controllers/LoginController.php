<?php

class LoginController extends \HXPHP\System\Controller
{
	public function logarAction()
	{
		//aproveitando a index
		$this->view->setFile('index');
	}
}