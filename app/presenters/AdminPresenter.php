<?php
/**
 * @author Tomáš Blatný
 */

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Context;


class AdminPresenter extends Presenter
{

	/** @var Context */
	private $context;

	public function __construct(Context $context)
	{
		parent::__construct();
		$this->context = $context;
	}


	public function renderDefault()
	{
		$this->template->responses = $this->context->table('response')->fetchAll();
	}


	public function actionDetail($id)
	{
		$this->template->response = $this->context->table('response')->get($id);
	}


}
