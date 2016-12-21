<?php

namespace JedenWeb\GalleryModule;

use Kdyby\Doctrine\EntityManager;
use Nette\Http\FileUpload;
use Nette\Object;
use Nette\Utils\Image;
use WebChemistry\Images\AbstractStorage;

/**
 * @method void onModify(Image $image)
 * @author Pavel Jurásek
 */
class MailruImageUploader extends Object
{

	/** @var callable[] */
	public $onModify;

	/** @var EntityManager */
	private $em;

	/** @var AbstractStorage */
	private $imageStorage;


	/**
	 * @param EntityManager $em
	 * @param AbstractStorage $imageStorage
	 */
	public function __construct(EntityManager $em, AbstractStorage $imageStorage)
	{
		$this->em = $em;
		$this->imageStorage = $imageStorage;
	}

	/**
	 * @param Gallery $gallery
	 * @param FileUpload $file
	 */
	public function upload(Gallery $gallery, FileUpload $file)
	{
		$image = $file->toImage();
		$this->onModify($image);

		$image = $this->imageStorage->saveImage($image, $file->getSanitizedName(), 'galleries/'.$gallery->getSlug());

		$photo = new Photo(basename($image), $gallery);

		$this->em->persist($photo);
		$this->em->flush();
	}

}
