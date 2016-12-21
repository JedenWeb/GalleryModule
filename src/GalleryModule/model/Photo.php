<?php

namespace App\GalleryModule;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @author Pavel Jurásek
 */
class Photo extends Item
{

	/** @var string */
	protected $discr = 'photo';

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $name;


	/**
	 * @param string $name
	 * @param Gallery
	 * @param string|NULL
	 */
	public function __construct($name, Gallery $gallery, $description = NULL)
	{
		parent::__construct($gallery, $description);

		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getDiscr()
	{
		return $this->discr;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param string
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

}
