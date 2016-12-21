<?php

namespace JedenWeb\GalleryModule\AdminModule;

use JedenWeb\GalleryModule\Gallery;
use App\AppModule\AdminModule\SecuredPresenter;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;

/**
 * @author Pavel Jurásek
 */
class GalleriesPresenter extends SecuredPresenter
{

	/** @var EntityManager @inject */
	public $em;

	/** @var EntityRepository */
	private $galleries;


	public function startup()
	{
		parent::startup();

		$this->galleries = $this->em->getRepository(Gallery::class);
	}


	/**
	 * @param array $pos
	 */
	public function handleSort(array $pos)
	{
		$i = 1;
		$ids = [];
		foreach ($pos as $p) {
			$ids[$i++] = (int) ltrim($p, 'row-');
		}

		$positions = array_flip($ids);

		/** @var Gallery $gallery */
		foreach ($this->galleries->findBy(['id' => $ids]) as $gallery) {
			$gallery->setPosition($positions[$gallery->getId()]);
		}

		$this->em->flush();

		$this->payload->success = TRUE;
		$this->sendPayload();
	}


	/**
	 * @secured
	 * @param int
	 */
	public function handleVisibility($gallery_id)
	{
		$gallery = $this->fetchGallery($gallery_id);

		$gallery->toggleVisibility();
		$this->em->flush();

		if ($this->isAjax()) {
			$this->redrawControl('content');
		} else {
			$this->redirect('this');
		}
	}


	/**
	 * @secured
	 * @param int
	 */
	public function handleDelete($gallery_id)
	{
		$gallery = $this->fetchGallery($gallery_id);

		$this->em->remove($gallery);
		$this->em->flush();

		$this->flashMessage(sprintf('Gallery \'%s\' deleted.', $gallery->getName()), 'info');

		if ($this->isAjax()) {
			$this->redrawControl('content');
		} else {
			$this->redirect('this');
		}
	}


	public function renderDefault()
	{
		$this->template->galleries = $this->galleries->findBy([], ['position' => 'ASC']);
	}


	/**
	 * @param int
	 *
	 * @return Gallery
	 */
	private function fetchGallery($gallery_id)
	{
		if (!$gallery = $this->galleries->find($gallery_id)) {
			$this->flashMessage('Gallery not found.', 'error');
			$this->redirect('default');
		}

		return $gallery;
	}

}
