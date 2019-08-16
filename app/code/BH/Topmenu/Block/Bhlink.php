<?php
 
namespace BH\Topmenu\Block;

class Bhlink extends \Magento\Framework\View\Element\Html\Link
{
/**
* Render block HTML.
*
* @return string
*/
protected function _toHtml()
    {
     if (false != $this->getTemplate()) {
     return parent::_toHtml();
     }
     return '<li><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
}