<?php
/**
 * Magecom
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magecom.net so we can send you a copy immediately.
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @copyright Copyright (c) ${YEAR} Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magecom_PriceUpdateManager_Model_Priceupdate class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Model_Priceupdate extends Mage_Core_Model_Abstract
{
    const UPDATE_ACTION_TYPE_BY_PERCENT = 0;
    const UPDATE_ACTION_TYPE_BY_FIXED   = 1;

    /**
     * Product collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_productCollection = null;

    protected $_updater = null;

    /**
     * Set product collection for price update
     *
     * @return Magecom_PriceUpdateManager_Model_Priceupdate
     */
    protected function _setProductCollection()
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addWebsiteFilter($this->getParams('website'))
            ->addAttributeToSelect(array('price', 'price_type', 'color'));

        $categoryIds = $this->getParams('category_ids');
        if (!in_array('all', $categoryIds)) {
            $productCollection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left');

            $filter = array(
                array('in' => $categoryIds)
            );
            if (in_array('none', $categoryIds)) {
                $categoryIds = array_diff($categoryIds, array('none'));
                $filter = array(
                    array('null' => true),
                    array('in' => $categoryIds)
                );
            }
            $productCollection->addAttributeToFilter('category_id', $filter);
        }

        if (array_key_exists('option', $this->getParams('attribute')) && !empty($this->getParams('attribute')['option'])) {
            $productCollection->addAttributeToFilter($this->getParams('attribute')['code'], array('eq' => $this->getParams('attribute')['option']));
        }

        $productCollection->getSelect()->group('entity_id');

        $this->_productCollection = (count($productCollection) === 0) ? null : $productCollection;

        return $this;
    }

    /**
     * Get product collection for price update
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_setProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Return calculated price
     *
     * @param float $price
     * @return float
     */
    protected function _calculatePrice($price)
    {
        if ($this->getParams('action_type') == self::UPDATE_ACTION_TYPE_BY_PERCENT) {
            return $price*(100 + $this->getParams('rate'))/100;
        }

        return $price + $this->getParams('rate');
    }

    /**
     * Proccessing product price update
     *
     * @return array
     */
    public function updatePrice()
    {
        $productCollection = $this->getProductCollection();
        $result = array(
            'success'   => 0,
            'error'     => 0,
            'skip'      => 0,
        );

        foreach ($productCollection as $_product) {
            $newPrice = $this->_calculatePrice($_product->getPrice());
            if ($newPrice < 0
                || ($_product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
                && $_product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC)) {
                $result['skip']++;
                continue;
            }
            try {
                $_product->setPrice($newPrice);
                $_product->getResource()->saveAttribute($_product, 'price');
                $result['success']++;
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'priceupdate.log');
                $result['error']++;
            }
        }

        return $result;
    }
}