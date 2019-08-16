<?php
namespace BH\CategoryList\Block;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\View\Element\Template;
class CategoryList extends \Magento\Framework\View\Element\Template
{
    protected $_categoryHelper;
    protected $_categoryFactory;
    protected $_category;
    protected $_categoryRepository;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        
        array $data = []
        
       // \Magento\Review\Model\Review $reviewFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
       // $this->_objectReview = $reviewFactory;
        parent::__construct($context, $data);
    }

    public function getCategory($categoryId)
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);
        return $this->_category;
    }
    
    /**
     * Get category object
     * Using $_categoryRepository
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategoryById($categoryId) 
    {
        return $this->_categoryRepository->get($categoryId);
    }

    /**
     * Get all children categories IDs
     *
     * @param boolean $asArray return result as array instead of comma-separated list of IDs
     * @return array|string
     */
    public function getAllChildren($asArray = false, $categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getAllChildren($asArray);
        } else {
            return $this->getCategory($categoryId)->getAllChildren($asArray);
        }
    }
 
    /**
     * Retrieve children ids comma separated
     *
     * @return string
     */
    public function getChildren($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getChildren();
        } else {
            return $this->getCategory($categoryId)->getChildren();
        }        
    }        
    
}