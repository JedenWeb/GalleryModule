<?php

namespace App\GalleryModule\DI;

use App\GalleryModule\AdminModule\IGalleryForm;
use App\GalleryModule\AdminModule\IItemDescriptionForm;
use App\GalleryModule\RouterFactory;
use Librette\Doctrine\Sortable\DI\SortableExtension;
use Nette\DI\CompilerExtension;
use Zenify\DoctrineBehaviors\DI\TimestampableExtension;

/**
 * @author Pavel Jurásek
 */
class GalleryExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$this->addExtensions();

		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('routerFactory'))
			->setClass(RouterFactory::class);

		$container->addDefinition($this->prefix('galleryForm'))
			->setImplement(IGalleryForm::class);

		$container->addDefinition($this->prefix('itemDescriptionForm'))
			  ->setImplement(IItemDescriptionForm::class);
	}

	private function addExtensions()
	{
		$extensions = [
			'timestampable' => TimestampableExtension::class,
			'sortable' => SortableExtension::class,
		];

		foreach ($extensions as $name => $extensionClass) {
			if (empty($this->compiler->getExtensions($extensionClass))) {
				$this->compiler->addExtension($this->prefix($name), $extensionClass);
			}
		}
	}

}
