<?php
/**
 *
**/
namespace FishPig\WordPress_ACF\Model\Plugin;

class Term extends AbstractPlugin
{
	/**
	 * Get the field key for get_field
	 *
	 * @param \FishPig\WordPress\Model\AbstractModel $object
	 * @return string
	**/
	public function getFieldIdKey(\FishPig\WordPress\Model\AbstractModel $object)
	{
		return $object instanceof \FishPig\WordPress\Model\Term
			? $object->getTaxonomy() . '_' . $object->getId()
			: '';
	}
}
