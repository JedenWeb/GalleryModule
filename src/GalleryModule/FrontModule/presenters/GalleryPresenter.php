<?php

namespace JedenWeb\GalleryModule\FrontModule;

use App\AppModule\FrontModule\BasePresenter;
use JedenWeb\GalleryModule\Gallery as GalleryEntity;
use Kdyby\Doctrine\EntityManager;

/**
 * @author Pavel Jurásek
 */
class GalleryPresenter extends BasePresenter
{

	/** @var EntityManager @autowire */
	public $em;


	public function renderDefault(GalleryEntity $gallery)
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
