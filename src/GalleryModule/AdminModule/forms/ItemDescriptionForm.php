<?php

namespace App\GalleryModule\AdminModule;

use App\GalleryModule\Item;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\IPresenter;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * @author Pavel Jurásek
 */
class ItemDescriptionForm extends Control
{

	/**
	 * @var Item
	 */
	private $item;

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var string
	 */
	private $templateFile = __DIR__ . '/ItemDescriptionForm.latte';


	public function __construct(Item $item, EntityManager $entityManager)
	{
		parent::__construct();

		$this->item = $item;
		$this->em = $entityManager;
	}


	/**
	 * @param  Nette\ComponentModel\IComponent
	 */
	protected function attached($presenter)
	{
		parent::attached($presenter);

		if ($presenter instanceof IPresenter) {
			$this['form']->setDefaults([
				'description' => $this->item->getDescription(),
			]);
		}
	}


	/***/
	public function render()
	{
		$this->template->item = $this->item;
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}


	protected function createComponentForm()
	{
		$form = new Form();

		$form->addProtection('Security token expired, please try again later.');

		$form->addText('description')
			->getControlPrototype()->placeholder = 'Description';

		$form->addSubmit('submit', 'Save');
		$form->onSuccess[] = [$this, 'handleSuccess'];

		return $form;
	}


	/**
	 * @param Form $form
	 * @param $values
	 */
	public function handleSuccess(Form $form, $values)
	{
		$this->item->setDescription($values->description);

		$this->em->flush();
	}


	/**
	 * @param string $templateFile
	 */
	public function setTemplateFile($templateFile)
	{
		$this->templateFile = $templateFile;
	}

}


interface IItemDescriptionForm
{

	/**
	 * @param Item $item
	 * @return ItemDescriptionForm
	 */
	public function create(Item $item);

}
