<?php

namespace App\GalleryModule\FrontModule;

use App\AppModule\FrontModule\BasePresenter;
use App\GalleryModule\Gallery as GalleryEntity;
use Kdyby\Doctrine\EntityManager;

/**
 * @author Pavel Jurásek
 */
class GalleriesPresenter extends BasePresenter
{

	/** @var EntityManager @autowire */
	public $em;


	public function renderDefault()
	{
		$this->template->galleries = $this->em->getRepository(GalleryEntity::class)->findBy([
			'visible' => TRUE,
		], ['position' => 'DESC']);
	}


	public function renderDetail(GalleryEntity $gallery)
	{
		$this->template->gallery = $gallery;
	}


	/**
	 * @param $name
	 * @param IGallery $factory
	 *
	 * @return Gallery
	 */
	protected function createComponentGallery($name, IGallery $factory)
	{
		return $factory->create();
	}

}
