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
 * @package Magecom_Masspriceupdate
 * @copyright Copyright (c) 2015 Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PriceUpdateManager data helper
 *
 * Magecom_PriceUpdateManager_Helper_Data class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_defaultWebsiteId = null;

    /**
     * Configuration paths for module settings
     */
    const XML_PATH_CLEAN_CACHE  = 'magecom_priceupdatemanager/general/clean_cache';
    const XML_PATH_REINDEX      = 'magecom_priceupdatemanager/general/reindex';
    const XML_PATH_ATTRIBUTE    = 'magecom_priceupdatemanager/general/attrubute_code';

    /**
     * Return current website id
     *
     * @return int
     */
    public function getDefaultWebsiteId() {
        if (is_null($this->_defaultWebsiteId)) {
            $websites = Mage::getModel('core/website')->getCollection()
                ->addFieldToFilter('is_default', 1);
            $website = $websites->getFirstItem();
            $this->_defaultWebsiteId = $website->getId();
        }

        return $this->_defaultWebsiteId;
    }

    /**
     * Return link to module config section
     *
     * @return string
     */
    public function getConfigLinkHtml()
    {
        $url = Mage::helper('adminhtml')->getUrl('*/system_config/edit', array('section' => 'magecom_priceupdatemanager'));

        return '<a href=' . $url . '>' . $this->__('Settings page') .  '</a>';
    }

    /**
     * Return config param for clear cache after price update
     *
     * @return mixed
     */
    public function getCleanCacheConfig()
    {
        return Mage::getStoreConfig(self::XML_PATH_CLEAN_CACHE, 0);
    }

    /**
     * Return config param for reindex after price update
     *
     * @return mixed
     */
    public function getReindexConfig()
    {
        return Mage::getStoreConfig(self::XML_PATH_REINDEX, 0);
    }

    /**
     * Get attribute data from module settings
     *
     * @return array|null
     */
    public function getAttribute()
    {
        $attributeCode = Mage::getStoreConfig(self::XML_PATH_ATTRIBUTE, null);
        if (!$attributeCode) {
            return null;
        }

        $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $attributeLabel = $attribute->getStoreLabel();
        if (!$attributeLabel) {
            return null;
        }

        $attributeOptions = array();
        if ($attribute->usesSource()) {
            $attributeOptions = $attribute->getSource()->getAllOptions();
        }
        $attributeOptions[0]['label'] = $this->__('Any values');

        return array(
            'code'      => $attributeCode,
            'label'     => $attributeLabel,
            'options'   => $attributeOptions
        );
    }

    /**
     * Get filterable catalog product attribute
     *
     * @return mixed
     */
    public function getFilterableAttributes()
    {
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection->setItemObjectClass('catalog/resource_eav_attribute')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->addFieldToFilter('additional_table.is_filterable', array('gt' => 0))
            ->addFieldToFilter('attribute_code', array('nin' => array('price', 'country_of_manufacture')))
            ->setOrder('position', 'ASC')
            ->load();

        return $collection->getData();
    }
}