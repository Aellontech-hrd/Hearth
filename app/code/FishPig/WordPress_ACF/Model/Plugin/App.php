<?php
/**
 *
**/
namespace FishPig\WordPress_ACF\Model\Plugin;

use \FishPig\WordPress\Model\App as Subject;

class App
{
	/**
	 * Get the core helper
	 *
	 * @param \FishPig\WordPress\Model\AbstractModel
	 * @param \Closure $callback
	 * @param string $key
	 * @return mixed
	**/
	public function aroundGetCoreHelper(Subject $app, \Closure $callback)
	{
		return \Magento\Framework\App\ObjectManager::getInstance()->create('FishPig\WordPress_ACF\Helper\Core');
	}
}
