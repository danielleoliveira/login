<?php 

class LoginAttempt extends \HXPHP\System\Model
{
	public static function totalDeTentativas($user_id)
	{
		return count(self::find_all_by_user_id($user_id));
	}

	public static function tentativasRestantes($user_id)
	{
		return intval(5-self::totalDeTentativas());
	}

	public static function registrarTentativa($user_id)
	{
		self::create(array(
			'user_id' => $user_id 
		));
	}

	public static function limparTentativas($user_id)
	{
		self::delete_all(array(
			'conditions' => array(
				'user_id = ?',
				$user_id
			)
		));
	}

	public static function existemTentativas($user_id)
	{
		//se meu total de tentativas for maior que 5 não posso mais tentar, caso contrário posso
		return self::totalDeTentativas() < 5 ? true : false; 
	}
}