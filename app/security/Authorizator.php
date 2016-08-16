<?php
/**
 * @author Tomáš Blatný
 */

namespace App\Security;

use Nette\Security\Permission;


class Authorizator extends Permission
{

	public function __construct()
	{
		// fluent interface

		$this->addRole('guest')
			->addRole('member', 'guest')
			->addRole('admin', 'member');

		$this->addResource('test')
			->addResource('result');

		$this->allow('guest', 'test', 'submit');

		$this->allow('admin', 'result', ['list', 'detail']);
	}

}
