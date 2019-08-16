<?php
namespace BH\Topmenu\Block;

class FullBreadcrumbs extends \Eadesigndev\FullBreadcrumbs\Block\FullBreadcrumbs
{

    public function getCategories($filtered_colection, $badCategories)
    {
        $separator = ' <span class="breadcrumbsseparator"></span> ';
        $categories = '';
        foreach ($filtered_colection as $categoriesData) {
            if (!in_array($categoriesData->getId(), $badCategories)) {
                $categories .= '<a href="' . $categoriesData->getUrl() . '">';
                $categories .= $categoriesData->getData('name') . '</a>' . $separator;
            }
        }
        return $categories;
    }

    public function getProductBreadcrumbs()
    {
        if ($this->isEnable()) {
            $separator = ' <span class="breadcrumbsseparator"></span> ';
            $product = $this->getProduct();
            $categoryIds = $this->getCategoryProductIds($product);

            $filtered_colection = $this->getFilteredCollection($categoryIds);

            $badCategories = $this->getBadCategories();

            $categories = $this->getCategories($filtered_colection, $badCategories);

            $home_url = '<a href="' . $this->_storeManager->getStore()->getBaseUrl() . '">Home</a>';
            return $home_url . $separator . $categories . '<span>' . $product->getName() . '</span>';
        }
    }
}