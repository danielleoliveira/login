<?php

class User extends \HXPHP\System\Model
{
	static $validates_presence_of = array(
		array(
			'name',
			'message' => 'O nome é um campo obrigatório.'
		),
		array(
			'email',
			'message' => 'O e-mail é um campo obrigatório.'
		),
		array(
			'username',
			'message' => 'O nome de usuário é um campo obrigatório.'
		),
		array(
			'password',
			'message' => 'A senha é um campo obrigatório.'
		)
	);

	static $validates_uniqueness_of = array(
			array(
				'username', 
				'message' => 'Já existe um usuário com este nome de usuário cadastrado.' 
			),
			array(
				'email', 
				'message' => 'Já existe um usuário com este e-mail cadastrado.' 
			)
	);

	public static function cadastrar(array $post)
	{
		$callbackObj = new \stdClass;
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();

		$role = Role::find_by_role('user');

		if (is_null($role)) {
			array_push($callbackObj->errors, 'A role user não existe. Contate o administrador');
			return $callbackObj;
		}

		$post = array_merge($post, array(
			'role_id' => $role->id,
			'status' => 1
		));

		$password = \HXPHP\System\Tools::hashHX($post['password']);

		$post = array_merge($post, $password);

		$cadastrar = self::create($post);

		if ($cadastrar->is_valid()) {
			$callbackObj->user = $cadastrar;
			$callbackObj->status = true;
			return $callbackObj;
		}

		$errors = $cadastrar->errors->get_raw_errors();

		foreach ($errors as $field => $message) {
			array_push($callbackObj->errors, $message[0]);
		}

		return $callbackObj;
	}

	public static function login(array $post)
	{
		$callbackObj = new \stdClass;
		$callbackObj->user = null;
		$callbackObj->status = false;
		//Code: Variável para exibir erro do plugin Auth.json no Controller 
		$callbackObj->code = null;
		//verificar se o usuário existe
		$user = self::find_by_username($post['username']);

		if(!is_null($user))
		{
			//criando um parâmetro para comparação
			$password = \HXPHP\System\Tools::hashHX($post['password'], $user->salt);

			//verificando se o usuário não está bloqueado
			if($user->status == 1)
			{
				if(loginAttempt::existemTentativas($user->id))
				{
					//comparando a senha digitada com a senha armazenada
					//se logar limpa as tentativas, senão adiciona mais uma tentativa
					if ($password['password'] == $user->password)
					{
						//alterando o callback já que o usuário está logado 
						$callbackObj->user = $user;
						$callbackObj->status = true;


						LoginAttempt::limparTentativas($user->id);
					}
					else
					{
						//mensagem do Auth.json para quando errar a senha
						$callbackObj->code = 'dados-incorretos';

						LoginAttempt::registrarTentativa($user->id);
					}
				}
				//caso estoure o número de tentativas, bloqueia o usuário, através o status
				else
				{
					//mensagem do Auth.json para usuário bloqueado por tentativas
					$callbackObj->code = 'usuário-bloqueado';

					$user->status = 0;
					//impede as validações na hora de atualizar o cadastro
					$user->save(false);
				}
			}
			else
			{
				//mensagem do Auth.json par dizer que o usuário foi bloqueado
				$callbackObj->code = 'usuário-bloqueado';

			}
		}
		else
		{
			//mensagem do Auth.json para dizer que o usuário não existe
			$callbackObj->code = 'usuário-inexistente';

		}

		return $callbackObj;
	}
}