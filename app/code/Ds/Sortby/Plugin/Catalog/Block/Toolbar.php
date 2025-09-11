<?php
namespace Ds\Sortby\Plugin\Catalog\Block;

class Toolbar
{
    public function aroundSetCollection(
    \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
    \Closure $proceed,
    $collection
    ) {

    $currentOrder = $subject->setCurrentOrder();
    $result = $proceed($collection);

    if (isset($_GET["order"]) && $_GET["order"]) {
        $subject->setData('_current_grid_order',$_GET["order"]);
        if ($_GET["order"] == 'low_to_high') {
            $subject->getCollection()->setOrder('price', 'asc');
            $subject->setCurrentOrder('low_to_high');
        } elseif ($_GET["order"] == 'high_to_low') {
            $subject->getCollection()->setOrder('price', 'desc');
        }
		
		
		if ($_GET["order"] == 'a_to_z') {
            $subject->getCollection()->setOrder('name', 'asc');
        } elseif ($_GET["order"] == 'z_to_a') {
            $subject->getCollection()->setOrder('name', 'desc');
        }
    }

    return $subject;
    }

}