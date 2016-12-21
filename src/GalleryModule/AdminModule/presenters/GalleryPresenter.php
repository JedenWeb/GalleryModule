<?php

namespace JedenWeb\GalleryModule\AdminModule;

use App\AppModule\AdminModule\SecuredPresenter;
use App\InvalidStateException;
use JedenWeb\GalleryModule\Gallery;
use JedenWeb\GalleryModule\Item;
use Echo511\Plupload\Control\IPluploadControlFactory;
use Echo511\Plupload\Control\PluploadControl;
use Echo511\Plupload\Entity\UploadQueue;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Application\UI\Multiplier;
use Tracy\Debugger;
use WebChemistry\Images\AbstractStorage;

/**
 * @author Pavel Jurásek
 */
class GalleryPresenter extends SecuredPresenter
{

	/** @var Gallery */
	public $gallery;

	/** @var EntityManager @inject */
	public $em;

	/** @var EntityRepository */
	public $galleries;

	/** @var EntityRepository */
	private $items;

	/** @var AbstractStorage @autowire */
	public $fileUploader;


	public function startup()
	{
		parent::startup();

		$this->galleries = $this->em->getRepository(Gallery::class);
		$this->items = $this->em->getRepository(Item::class);
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

		/** @var Item $item */
		foreach ($this->items->findBy(['gallery' => $this->gallery]) as $item) {
			$item->setPosition($positions[$item->getId()]);
		}

		$this->em->flush();

		$this->payload->success = TRUE;
		$this->sendPayload();
	}


	/**
	 * @secured
	 * @param int
	 */
	public function handleVisibility($item_id)
	{
		$item = $this->fetchItem($item_id);

		$item->toggleVisibility();
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
	public function handleDelete($item_id)
	{
		$item = $this->fetchItem($item_id);

		$this->em->remove($item);
		$this->em->flush();

		$this->flashMessage('Item \''. $item->getName() .'\' deleted.', 'success');

		if ($this->isAjax()) {
			$this->redrawControl('content');
		} else {
			$this->redirect('this');
		}
	}


	/**
	 * @param int
	 */
	public function actionEdit($gallery_id)
	{
		$this->fetchGallery($gallery_id);
		$this->template->gallery = $this->gallery;
	}


	/**
	 * @param int
	 */
	private function fetchGallery($gallery_id)
	{
		if (!$this->gallery = $this->galleries->find($gallery_id)) {
			$this->flashMessage('Gallery not found.', 'error');
			$this->redirect('Galleries:');
		}
	}


	/**
	 * @param string
	 *
	 * @return Item
	 */
	private function fetchItem($item_id)
	{
		if (!$item = $this->items->find($item_id)) {
			$this->flashMessage('Item not found.', 'error');
			$this->redirect('this');
		}

		return $item;
	}


	/**
	 * @param $name
	 * @param IGalleryForm $factory
	 *
	 * @return GalleryForm
	 */
	protected function createComponentGalleryForm($name, IGalleryForm $factory)
	{
		$this->addComponent($form = $factory->create($this->gallery), $name);
		$form['form']->onSuccess[] = function($form, $values) {
			$this->flashMessage('Gallery \''.$values->name.'\' saved.', 'success');
			$this->redirect('Galleries:');
		};

		return $form;
	}


	/**
	 * @param $name
	 * @param IItemDescriptionForm $factory
	 *
	 * @return Multiplier
	 */
	protected function createComponentItemDescriptionForm($name, IItemDescriptionForm $factory)
	{
		$parentName = $name;
		return new Multiplier(function($name, Multiplier $multiplier) use ($parentName, $factory) {
			/** @var Item $item */
			$item = $this->items->find($name);

			$multiplier->addComponent($form = $factory->create($item), $name);
			$form['form']->onSuccess[] = function() use ($parentName, $name) {
				$this->payload->receiver = $parentName;
				$this->payload->id = $name;
				$this->sendPayload();
			};
			return $form;
		});
	}


	/**
	 * @param $name
	 * @param IPluploadControlFactory $factory
	 *
	 * @return PluploadControl
	 */
	protected function createComponentPlupload($name, IPluploadControlFactory $factory)
	{
		$plupload = $factory->create();
		$plupload->allowedExtensions = 'png,jpg,gif';
		$plupload->onFileUploaded[] = $this->handleFileUploaded;

		return $plupload;
	}


	/**
	 * @param UploadQueue $queue
	 */
	public function handleFileUploaded(UploadQueue $queue)
	{
		try {
			$this->fileUploader->saveUpload($queue->getLastUpload(), $this->gallery);
			$this->redrawControl('content');

		} catch (InvalidStateException $e) {
			Debugger::log($e);
			$this->flashMessage('Uploaded file is not an image.', 'error');
			$this->redrawControl('flashes');
		}
	}

}
