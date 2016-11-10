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
 * Magecom_PriceUpdateManager_Model_Observer class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Model_Observer
{

    /**
     * Clear cache after price update
     *
     * @return Magecom_PriceUpdateManager_Model_Observer
     */
    public function clearCache()
    {
        if(Mage::helper('priceupdatemanager')->getCleanCacheConfig()){
            Mage::app()->cleanCache();
        }
        return $this;
    }

    /**
     * Reindex catalog product price after price apdate
     *
     * @return Magecom_PriceUpdateManager_Model_Observer
     */
    public function reindexPrice()
    {
        if(Mage::helper('priceupdatemanager')->getReindexConfig()){
            $process = Mage::getModel('index/indexer')
                ->getProcessByCode('catalog_product_price');
            $process->reindexAll();
        }
        return $this;
    }
}