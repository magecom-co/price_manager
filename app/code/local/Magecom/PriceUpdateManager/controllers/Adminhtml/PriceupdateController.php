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
 * Magecom_PriceUpdateManager_Adminhtml_PriceupdateController class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Adminhtml_PriceupdateController extends Mage_Adminhtml_Controller_Action
{
    /**
     *  Price updater page
     */
    public function indexAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Mass Price Update'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/priceupdatemanager');
        $this->_addBreadcrumb(Mage::helper('priceupdatemanager')->__('Catalog'), Mage::helper('priceupdatemanager')->__('Mass Price Update'));
        $this->renderLayout();
    }

    /**
     * Get category list ajax request
     */
    public function categorylistAction()
    {
        try {
            $websiteId = $this->getRequest()->getParam('website', Mage::helper('priceupdatemanager')->getDefaultWebsiteId());

            $block = $this->getLayout()->createBlock('priceupdatemanager/category_list', 'category.list', array('website' => $websiteId));
            $response = array(
                'result'    => 'SUCCESS',
                'block'     => $block->toHtml()
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        } catch (Exception $e) {
            $response = array(
                'result'    => 'ERROR',
                'message'     => $e->getMessage()
            );
            Mage::log($e->getMessage(), null, 'priceupdatemanager.log');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }

    }

    /**
     * Proccessing update price ajax request
     */
    public function updateAction()
    {
        try {
            $data = $this->getRequest()->getPost();
            $updater = Mage::getModel('priceupdatemanager/priceupdate')->setParams($data);

            if (is_null($updater->getProductCollection())) {
                $this->_getSession()->addError(Mage::helper('priceupdatemanager')->__('There are no products matching the selection.'));
            } else {
                $resultData = $updater->updatePrice();

                if (Mage::helper('priceupdatemanager')->getCleanCacheConfig()) {

                    Mage::dispatchEvent('magecom_priceupdate_clearcache_after');
                }
                if (Mage::helper('priceupdatemanager')->getReindexConfig()) {
                    Mage::dispatchEvent('magecom_priceupdate_reindex_after');
                }

                if ($resultData['success']) {
                    $this->_getSession()->addSuccess(Mage::helper('priceupdatemanager')->__('%s product(s) was(were) succussfully updated.', $resultData['success']));
                }
                if ($resultData['error']) {
                    $this->_getSession()->addError(Mage::helper('priceupdatemanager')->__('%s product(s) can not updated. View log file for details.', $resultData['error']));
                }
                if ($resultData['skip']) {
                    $this->_getSession()->addNotice(Mage::helper('priceupdatemanager')->__('%s product(s) was(were) skiped with new price less zero or products have dynamic price.', $resultData['skip']));
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $block = $this->getLayout()->getMessagesBlock();
        $block->addMessages($this->_getSession()->getMessages(true));
        $result = array(
            'result'    => $block->toHtml()
        );

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}