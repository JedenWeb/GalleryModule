<?php

namespace App\GalleryModule;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JedenWeb\Doctrine\Visible\Visible;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Librette\Doctrine\Sortable\ISortable;
use Librette\Doctrine\Sortable\TSortable;
use Nette\Utils\Strings;

/**
 * @ORM\Entity()
 * @author Pavel Jurásek
 */
class Gallery implements ISortable
{

	use Identifier;
	use Timestampable;
	use TSortable;
	use Visible;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $slug;

	/**
	 * @ORM\Column(type="datetime", nullable=TRUE, name="recorded_date")
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * @ORM\OneToMany(targetEntity="Item", mappedBy="gallery", cascade={"persist"})
	 * @ORM\OrderBy({"position" = "ASC"})
	 * @var Item[]|ArrayCollection
	 */
	protected $items;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	protected $itemCount = 0;


	/**
	 * @param string $name
	 * @param \DateTime|NULL $date
	 */
	public function __construct($name, \DateTime $date = NULL)
	{
		$this->setName($name);
		$this->date = $date;
		$this->items = new ArrayCollection;
	}


	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
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
		$this->slug = Strings::webalize($name);
	}


	/**
	 * @return \DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}


	/**
	 * @param \DateTime $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}


	/**
	 * @return int
	 */
	public function getItemCount()
	{
		return $this->itemCount;
	}


	/**
	 * @return Item|NULL
	 */
	public function getFirstItem()
	{
		return $this->items->first();
	}


	/**
	 * @return Item[]
	 */
	public function getItems()
	{
		return $this->items->toArray();
	}


	/**
	 * @param Item $item
	 */
	public function addItem(Item $item)
	{
		$this->items->add($item);
		$this->itemCount++;
	}


	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function removeItem(Item $item)
	{
		$result = $this->items->removeElement($item);
		if ($result) {
			$this->itemCount--;
		}

		return $result;
	}


	/**
	 * @param int $itemIndex
	 *
	 * @return Item|NULL
	 */
	public function removeItemByIndex($itemIndex)
	{
		$item = $this->items->remove($itemIndex);
		if ($item !== NULL) {
			$this->itemCount--;
		}

		return $item;
	}

}
