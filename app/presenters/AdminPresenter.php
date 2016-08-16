<?php
/**
 * @author Tomáš Blatný
 */

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Database\Context;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;


class AdminPresenter extends BasePresenter
{

	/** @var Context */
	private $context;

	public function __construct(Context $context)
	{
		parent::__construct();
		$this->context = $context;
	}


	public function actionList()
	{
		if (!$this->user->isAllowed('result', 'list')) {
			$this->flashMessage('Please log in before accessing administration.', 'error');
			$this->redirect('Admin:default');
		}
		$this->template->responses = $this->context->table('response')->fetchAll();
	}


	public function actionDetail($id)
	{
		if (!$this->user->isAllowed('result', 'detail')) {
			$this->flashMessage('Please log in before accessing administration.', 'error');
			$this->redirect('Admin:default');
		}
		$this->template->response = $this->context->table('response')->get($id);
	}


	protected function createComponentRegisterForm()
	{
		$form = new Form;

		$form->addText('nick', 'Nick')
			->setRequired('Please enter nick.');

		$form->addPassword('password', 'Password')
			->setRequired('Please enter password.');

		$form->addPassword('password2', 'Password for check')
			->setRequired('Please enter password for check.')
			->addRule($form::EQUAL, 'Passwords doesn\'t match.', $form['password'])
			->setOmitted();

		$form->addSubmit('register', 'Register');

		$form->onSuccess[] = function ($form, $values) {
			if ($this->context->table('user')->where('nick', $values->nick)->fetch()) {
				$this->flashMessage('User with this name already exists', 'error');
				$this->redirect('this');
			}

			$this->context->table('user')->insert([
				'nick' => $values->nick,
				'password' => Passwords::hash($values->password),
				'role' => 'admin',
			]);
			$this->loginUser($values->nick, $values->password);
			$this->flashMessage('Registration completed successfully.', 'success');
			$this->redirect('this');
		};

		return $form;
	}


	protected function createComponentLoginForm()
	{
		$form = new Form;

		$form->addText('nick', 'Nick')
			->setRequired('Please enter nick.');

		$form->addPassword('password', 'Password')
			->setRequired('Please enter password.');

		$form->addSubmit('login', 'Login');

		$form->onSuccess[] = function ($form, $values) {
			$this->loginUser($values->nick, $values->password);
		};

		return $form;
	}


	private function loginUser($nick, $password)
	{
		try {
			$this->user->login($nick, $password);
			$this->flashMessage('Login successful.', 'success');
			$this->redirect('Admin:list');
		} catch (AuthenticationException $e) {
			$this->flashMessage($e->getMessage(), 'error');
			$this->redirect('this');
		}
	}


}
