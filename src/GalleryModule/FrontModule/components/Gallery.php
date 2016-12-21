<?php

namespace App\GalleryModule\FrontModule;

use App\GalleryModule;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Application\UI\Control;
use WebChemistry\Images\AbstractStorage;

/**
 * @author Pavel Jurásek
 */
class Gallery extends Control
{

	/**
	 * @var EntityRepository
	 */
	private $galleries;

	/**
	 * @var AbstractStorage
	 */
	private $imageStorage;

	/**
	 * @var string
	 */
	private $templateFile = __DIR__ . '/Gallery.latte';


	/**
	 * @param EntityManager $entityManager
	 * @param AbstractStorage $imageStorage
	 */
	public function __construct(EntityManager $entityManager, AbstractStorage $imageStorage)
	{
		parent::__construct();

		$this->galleries = $entityManager->getRepository(GalleryModule\Gallery::class);
		$this->imageStorage = $imageStorage;
	}


	/**
	 * @param GalleryModule\Gallery|int $gallery
	 */
	public function render($gallery)
	{
		if (is_scalar($gallery)) {
			$gallery = $this->galleries->find($gallery);
		}

		$this->template->gallery = $gallery;

		$this->template->imageStorage = $this->imageStorage;
		$this->template->setFile($this->templateFile);
		$this->template->render();
	}


	/**
	 * @param string $templateFile
	 */
	public function setTemplateFile($templateFile)
	{
		$this->templateFile = $templateFile;
	}

}


interface IGallery
{

	/**
	 * @return Gallery
	 */
	public function create();

}
