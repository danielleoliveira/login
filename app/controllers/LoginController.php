<?php

class LoginController extends \HXPHP\System\Controller
{
	public function __construct($configs)
	{
		//passando as configurações do config.php
		parent::__construct($configs);

		//carregando o serviço de autenticação
		//o true é o redirect, ou seja, define se o usuário vai ser redirecionado ou não 
		$this->load(
			'Services\Auth', 
			$configs->auth->after_login, 
			$configs->auth->after_logout,
			true
		);

		//checar se está logado e redirecionar para a página adequada.
		//true é publica, false é privada
		$this->auth->redirectCheck(true);
	}

	public function logarAction()
	{
		//aproveitando a index
		$this->view->setFile('index');

		$post = $this->request->post();

		if(!empty($post))
		{
			$login = User::login($post);

			//se meu usuário está logado
			if($login->status == true)
			{
				$this->auth->login($login->user->id, $login->user->username);
			}
			//senão
			else
			{
				//carregando as mensagens de erro do auth.json para exibir 
				$this->load('Modules\Messages', 'auth');
				$this->messages->setBlock('alerts');
				$error = $this->messages->getByCode($login->code);

				$this->load('Helpers\Alert', $error);
			}
		}
	}
}