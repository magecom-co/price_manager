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
 * Magecom_PriceUpdateManager_Block_Adminhtml_Update class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Block_Adminhtml_Update extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magecom_PriceUpdateManager_Block_Adminhtml_Update constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'priceupdatemanager';
        $this->_controller = 'adminhtml';
        $this->_mode = 'update';
        $this->_buttons = array();
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('priceupdatemanager')->__('Price updater');
    }
}