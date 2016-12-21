<?php

namespace JedenWeb\GalleryModule\AdminModule;

use JedenWeb\GalleryModule\Gallery;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\IPresenter;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use WebChemistry\Images\AbstractStorage;

/**
 * @author Pavel Jurásek
 */
class GalleryForm extends Control
{

	/**
	 * @var Gallery
	 */
	private $gallery;

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var AbstractStorage
	 */
	private $imageStorage;

	/**
	 * @var string
	 */
	private $templateFile = __DIR__ . '/GalleryForm.latte';


	/**
	 * @param Gallery|NULL $gallery
	 * @param EntityManager $entityManager
	 * @param AbstractStorage $imageStorage
	 */
	public function __construct(Gallery $gallery = NULL, EntityManager $entityManager, AbstractStorage $imageStorage)
	{
		parent::__construct();

		$this->gallery = $gallery;
		$this->em = $entityManager;
		$this->imageStorage = $imageStorage;
	}


	/**
	 * @param  Nette\ComponentModel\IComponent
	 */
	protected function attached($presenter)
	{
		parent::attached($presenter);

		if ($presenter instanceof IPresenter && $this->gallery) {
			$this['form']->setDefaults([
				'name' => $this->gallery->getName(),
			]);
		}
	}


	/***/
	public function render()
	{
		$this->template->gallery = $this->gallery;
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}


	protected function createComponentForm()
	{
		$form = new Form();

		$form->addProtection('Security token expired, please try again.');

		$form->addText('name', 'Name')
			->setRequired('Gallery name is mandatory.');

		$form->addText('date', 'Date')
			->setType('date');

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
		if ($gallery = $this->gallery) {
			$gallery->setName($values->name);
		} else {
			$gallery = new Gallery($values->name);

			$this->em->persist($gallery);
		}

		$date = \DateTime::createFromFormat('Y-m-d', $values->date);

		if ($date !== FALSE) {
			$gallery->setDate($date);
		}

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


interface IGalleryForm
{

	/**
	 * @param Gallery|NULL $gallery
	 * @return GalleryForm
	 */
	public function create(Gallery $gallery = NULL);

}
