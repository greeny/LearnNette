<?php
/**
 * @author Tomáš Blatný
 */

namespace App\Security;

use Nette\Database\Context;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;


class Authenticator implements IAuthenticator
{

	/** @var Context */
	private $context;


	public function __construct(Context $context)
	{
		$this->context = $context;
	}


	public function authenticate(array $credentials)
	{
		list($nick, $password) = $credentials;

		$row = $this->context->table('user')->where('nick', $nick)->fetch();

		if (!$row) {
			throw new AuthenticationException('User not found.');
		}

		if (!Passwords::verify($password, $row->password)) {
			throw new AuthenticationException('Invalid password.');
		}

		return new Identity($row->id, [$row->role], [
			'nick' => $row->nick,
		]);
	}

}
