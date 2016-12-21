<?php

namespace JedenWeb\GalleryModule;

use Echo511\Plupload\Entity\Upload;
use Echo511\Plupload\Entity\UploadQueue;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;
use Nette\Utils\Image;
use Nette\Utils\Strings;
use Nette\Utils\UnknownImageFileException;
use WebChemistry\Images\AbstractStorage;

/**
 * @method void onModify(Image $image)
 * @author Pavel Jurásek
 */
class PluploadImageUploader extends Object
{

	/** @var callable[] */
	public $onModify;

	/** @var EntityManager */
	private $em;

	/** @var AbstractStorage */
	private $imageStorage;


	public function __construct(EntityManager $em, AbstractStorage $imageStorage)
	{
		$this->em = $em;
		$this->imageStorage = $imageStorage;
	}


	/**
	 * @param Gallery $gallery
	 * @param UploadQueue $queue
	 */
	public function upload(Gallery $gallery, UploadQueue $queue)
	{
		/** @var Upload $file */
		$file = $queue->getLastUpload();

		$this->validateType($file);

		$image = Image::fromFile($file->getFilename());
		$this->onModify($image);

		$imageName = $this->imageStorage->saveImage($image, $this->sanitizeName($file->getName()), 'galleries/'.$gallery->getSlug());

		$photo = new Photo(basename($imageName), $gallery);

		$this->em->persist($photo);
		$this->em->flush();
	}


	/**
	 * @param Upload $file
	 *
	 * @throws UnknownImageFileException
	 */
	private function validateType(Upload $file)
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file->getFilename());

		if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
			throw new UnknownImageFileException(sprintf('Uploaded file is not an image. \'%s\' is %s.', $file->getFilename(), $mime));
		}
	}


	/**
	 * @param string
	 *
	 * @return string
	 */
	private function sanitizeName($name)
	{
		return trim(Strings::webalize($name, '.', FALSE), '.-');
	}

}
