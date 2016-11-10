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
 * @copyright Copyright (c) 2015 Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PriceUpdateManager category helper
 *
 * Magecom_PriceUpdateManager_Helper_Category class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Helper_Category extends Magecom_PriceUpdateManager_Helper_Data
{
    protected $_defaultCategoryOptions = array();
    protected $_emptyCategoryOptions = array();

    /**
     * Magecom_PriceUpdateManager_Helper_Category constructor.
     */
    public function __construct()
    {
        $this->_defaultCategoryOptions = array(
            array(
                'value' => 'all',
                'label' => $this->__('Update all products'),
            ),
            array(
                'value' => 'none',
                'label' => $this->__('Products without category'),
            )
        );

        $this->_emptyCategoryOptions = array(
            array(
                'value' => 'all',
                'label' => $this->__('Update all products')
            )
        );
    }

    /**
     * Get category list for current website
     *
     * @param null|Mage_Core_Model_Website $website
     * @return array
     */
    public function getCategoryList($website = null)
    {
        $categories = array();

        if (is_null($website)) {
            $website = Mage::getModel('core/website')->load($this->getDefaultWebsiteId());
        }

        try {
            foreach ($website->getGroups() as $store) {
                $rootId = $store->getRootCategoryId();
                $categoryCollection = Mage::getModel('catalog/category')->getResource()
                    ->getCategories($rootId, 0, true, true);
                if (count($categoryCollection) === 0) {
                    return $this->_emptyCategoryOptions;
                }
                foreach ($categoryCollection as $category) {
                    $categories[] = array(
                        'value' => $category->getId(),
                        'label' => $category->getName() . ' (' . $category->getProductCount() .')'
                    );
                }
            }
            $categories = array_merge($this->_defaultCategoryOptions, $categories);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'priceupdate.log');
            return $this->_emptyCategoryOptions;

        }

        return $categories;
    }
}