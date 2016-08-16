<?php
/**
 * @author Tomáš Blatný
 */

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;


class TestPresenter extends BasePresenter
{

	/** @var Context */
	private $context;

	private $questions;


	public function __construct(Context $context)
	{
		parent::__construct();
		$this->context = $context;
	}


	public function actionDefault()
	{
		$this->questions = $this->context->table('question')->fetchAll();
	}


	public function renderDefault()
	{
		$this->template->questions = $this->questions;
	}


	protected function createComponentTestForm()
	{
		$form = new Form;

		foreach ($this->questions as $question) {
			$form->addRadioList($question->id, $question->title, [
				1 => $question->answer1,
				2 => $question->answer2,
				3 => $question->answer3,
				4 => $question->answer4,
				5 => $question->answer5,
			])->setRequired('Please answer question: ' . $question->title);
		}

		$form->addText('name', 'Your name')
			->setRequired('Please insert your name.');

		$form->addSelect('sex', 'Sex', [
			'm' => 'Male',
			'f' => 'Female',
		])
			->setPrompt('Select')
			->setRequired('Please select your sex.');

		$form->addSubmit('send', 'Send');

		$form->onSuccess[] = function ($form, $values) {

			$response = $this->context->table('response')->insert([
				'name' => $values->name,
				'sex' => $values->sex,
			]);

			foreach ($values as $questionId => $answer) {
				if (in_array($questionId, ['name', 'sex'], TRUE)) {
					continue;
				}
				$this->context->table('answer')->insert([
					'question_id' => $questionId,
					'response_id' => $response->id,
					'answer' => $answer,
				]);
			}

			$this->flashMessage('Thank you for your time.', 'success');

			$this->redirect('Test:default');

		};

		return $form;
	}

}
