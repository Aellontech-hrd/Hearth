<?php
/**
 * @category FishPig
 * @package FishPig_WordPress_WordPress_ACF
 * @author Ben Tideswell
 */
namespace FishPig\WordPress_ACF\Helper;

use \FishPig\WordPress_ACF\Helper\Core as CoreHelper;

class Data
{
	/**
	 * @var \FishPig\WordPress\Model\App
	**/
	protected $app = null;
	
	/**
	 * @var CoreHelper
	**/
	protected $coreHelper = null;
	
	/**
	 * @return void
	 */
	public function __construct()
	{		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$this->app = $objectManager->get('\FishPig\WordPress\Model\App');
		$this->coreHelper = $objectManager->get('\FishPig\WordPress_ACF\Helper\Core');
	}
	
	/**
	 * Get an ACF Field value
	 *
	 * @param string $key
	 * @param mixed $scope = null
	 * @return mixed
	**/
	public function getField($key, $scope = null)
	{
		try {
			$this->coreHelper->startWordPressSimulation();

			if (function_exists('get_field')) {
				if ($value = get_field($key, $scope)) {
					$this->coreHelper->endWordPressSimulation();
					
					return $this->_fixReturnValue($value);
				}
			}

			$this->coreHelper->endWordPressSimulation();
		}
		catch (\Exception $e) {
			$this->coreHelper->endWordPressSimulation();
			
			throw $e;
		}
		
		return null;
	}
	
	/**
	 * @param string $key
	 * @return mixed
	**/
	public function getOptionsField($key)
	{
		return $this->getField($key, 'options');
	}
	
	/*
	 *
	 *
	 */
	protected function _fixReturnValue($value)
	{
		if ($value) {
			if (is_array($value)) {
				foreach($value as $k => $v) {
					$value[$k] = $this->_fixReturnValue($v);
				}
			}
			else if (is_object($value)) {
				$class = get_class($value);
				
				if ($class === 'WP_Post') {
					$value = $this->_getObjectManager()->create('FishPig\WordPress\Model\PostFactory')->create()->load($value->ID)->setWPPostObject($value);
				}
				else if ($class === 'WP_Term') {
					$value = $this->_getObjectManager()->create('FishPig\WordPress\Model\TermFactory')->create()->load($value->term_id)->setWPTermObject($value);
				}
				else {
					exit('Class not transposed: ' . get_class($value));
				}
			}
		}

		return $value;
	}

	/*
	 *
	 *
	 */
	protected function _getObjectManager()
	{
		return \Magento\Framework\App\ObjectManager::getInstance();
	}
}
