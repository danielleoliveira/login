<?php

class CadastroController extends \HXPHP\System\Controller
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
	public function cadastrarAction()
	{
		$this->view->setFile('index');

		$this->request->setCustomFilters(array(
			'email' => FILTER_VALIDATE_EMAIL
		));

		$post = $this->request->post();

		if (!empty($post)) {
			$cadastrarUsuario = User::cadastrar($post);

			if ($cadastrarUsuario->status === false) {
				$this->load('Helpers\Alert', array(
					'danger',
					'Ops! Não foi possível efetuar seu cadastro. <br> Verifique os erros abaixo:',
					$cadastrarUsuario->errors
				));
			}
			else
			{
				$this->auth->login($cadastrarUsuario->user->id, $cadastrarUsuario->user->username); 
			}
		}
	}
}