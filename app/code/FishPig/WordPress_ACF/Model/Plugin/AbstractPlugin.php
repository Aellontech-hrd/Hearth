<?php
/**
 *
**/
namespace FishPig\WordPress_ACF\Model\Plugin;

abstract class AbstractPlugin
{
	/**
	 * @var \FishPig\WordPress_ACF\Helper\Data
	**/
	protected $_helper = null;
	
	/**
	 * @return \FishPig\WordPress_ACF\Helper\Core
	**/
	protected function _getHelper()
	{
		if (is_null($this->_helper)) {
			$this->_helper = \Magento\Framework\App\ObjectManager::getInstance()->create('\FishPig\WordPress_ACF\Helper\Data');
		}
		
		return $this->_helper;
	}

	/**
	 * Get the post meta field value
	 *
	 * @param \FishPig\WordPress\Model\AbstractModel
	 * @param \Closure $callback
	 * @param string $key
	 * @return mixed
	**/
	public function aroundGetMetaValue(\FishPig\WordPress\Model\AbstractModel $object, \Closure $callback, $key)
	{
		if (!$this->isSafeToSkipKey($key)) {
			if (($value = $this->_getHelper()->getField($key, $this->getFieldIdKey($object))) !== null) {
				return $value;
			}
		}
		
		return $callback($key);
	}
	
	/**
	 * Determine whether key can be skipped
	 *
	 * @param string $key
	 * @return bool
	**/
	public function isSafeToSkipKey($key)
	{
		return strpos($key, '_yoast') === 0;
	}
	
	/**
	 * Get the field key for get_field
	 *
	 * @param \FishPig\WordPress\Model\AbstractModel $object
	 * @return string
	**/
	public function getFieldIdKey(\FishPig\WordPress\Model\AbstractModel $object)
	{
		return $object->getId();
	}
}
