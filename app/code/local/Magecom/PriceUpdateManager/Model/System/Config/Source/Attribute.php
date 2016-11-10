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
 * Magecom_PriceUpdateManager_Model_System_Config_Source_Attribute class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Model_System_Config_Source_Attribute
{
    /**
     * Used in creating options for Attribute filter config value selection
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => '',
                'label' => Mage::helper('priceupdatemanager')->__('Choose an attribute...')
            )
        );
        $attributeCollection = Mage::helper('priceupdatemanager')->getFilterableAttributes();
        foreach ($attributeCollection as $attribute) {
            $options[] = array(
                'value' => $attribute['attribute_code'],
                'label' => $attribute['frontend_label']
            );
        }

        return $options;
    }
}



