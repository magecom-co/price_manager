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
 * Magecom_PriceUpdateManager_Block_Category_List class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Block_Category_List extends Mage_Core_Block_Template
{
    private $_websiteId = null;
    private $_website   = null;

    /**
     * Get current website id
     *
     * @return int
     */
    protected function _getWebsiteId()
    {
        if (is_null($this->_websiteId)) {
            $this->_websiteId = $this->getData('website');
            if (empty($this->_websiteId)) {
                $this->_websiteId = Mage::helper('priceupdatemanager')->getDefaultWebsiteId();
                return $this->_websiteId;
            }
        }

        return $this->_websiteId;
    }

    /**
     * Get current website object
     *
     * @return Mage_Core_Model_Website
     */
    protected function _getWebsite()
    {
        if (is_null($this->_website)) {
            $this->_website = Mage::getModel('core/website')->load($this->_getWebsiteId());
        }

        return $this->_website;
    }

    /**
     * @return array
     */
    protected function _getCategoryList()
    {
        return Mage::helper('priceupdatemanager/category')->getCategoryList($this->_getWebsite());
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $helper = Mage::helper('priceupdatemanager');
        $validationClass = 'class="validate-one-required-by-name"';
        $html = '';
        $options = $this->_getCategoryList();

        if (count($options) === 0) {
            $html .= '<li><input onclick="PriceUpdater.updateCheckboxes(this);" type="checkbox" ' . $validationClass . ' value="all" name="category_ids[]"><label>' . $helper->__('Select all categories') . '</label></li>';
            return $html;
        }

        foreach ($options as $option) {
            $html .= '<li><input onclick="PriceUpdater.updateCheckboxes(this);" type="checkbox" ' . $validationClass . ' value="' . $option['value'] . '" name="category_ids[]"><label>' . $option['label'] . '</label></li>';
        }

        return $html;
    }
}