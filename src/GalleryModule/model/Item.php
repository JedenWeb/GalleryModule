<?php

namespace App\GalleryModule;

use Doctrine\ORM\Mapping as ORM;
use JedenWeb\Doctrine\Visible\Visible;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Librette\Doctrine\Sortable\ISortable;
use Librette\Doctrine\Sortable\TSortable;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gallery_item")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @author Pavel Jurásek
 */
abstract class Item implements ISortable
{

	use Identifier;
	use Timestampable;
	use TSortable;
	use Visible;


	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $description;

	/**
	 * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="items", cascade={"persist"})
	 * @var Gallery
	 */
	protected $gallery;


	/**
	 * @param Gallery $gallery
	 * @param string|NULL $description
	 */
	public function __construct(Gallery $gallery, $description = NULL)
	{
		$this->gallery = $gallery;
		$gallery->addItem($this);

		$this->description = $description;
	}


	/**
	 * @return string
	 */
	abstract public function getName();


	/**
	 * @return string
	 */
	abstract public function getDiscr();


	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

}
