<?php
/**
 * @author Tomáš Blatný
 */

namespace App\Presenters;

use Nette\Application\UI\Presenter;


abstract class BasePresenter extends Presenter
{

	public function handleLogout()
	{
		$this->user->logout();
		$this->redirect('Homepage:default');
	}

}
