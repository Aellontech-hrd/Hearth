<?php
/**
 *
**/
namespace FishPig\WordPress_ACF\Model\Plugin;

class Post extends AbstractPlugin
{
	/**
	 * Determine whether key can be skipped
	 *
	 * @param string $key
	 * @return bool
	**/
	public function isSafeToSkipKey($key)
	{
		return in_array($key, array('_wp_page_template')) || parent::isSafeToSkipKey($key);
	}
}
