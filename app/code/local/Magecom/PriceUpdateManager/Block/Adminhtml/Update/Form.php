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
 * Magecom_PriceUpdateManager_Block_Adminhtml_Update_Form class
 *
 * @category Magecom
 * @package Magecom_PriceUpdateManager
 * @author  Magecom
 */
class Magecom_PriceUpdateManager_Block_Adminhtml_Update_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form product updater
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('priceupdatemanager');

        $form = new Varien_Data_Form(array(
            'id'        => 'priceupdate_form',
            'action'    => Mage::helper('adminhtml')->getUrl('adminhtml/priceupdate/update')
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Price updater')));

        $fieldset->addField('website', 'select',
            array(
                'name'      => 'website',
                'label'     => $helper->__('Website'),
                'title'     => $helper->__('Website'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(),
                'onchange'  => 'PriceUpdater.getCategoryListByWebsite(this.value);',
                'value'     => $helper->getDefaultWebsiteId()
            )
        );
        $fieldset->addType('category_checkboxes', Magecom_PriceUpdateManager_Block_Adminhtml_Data_Form_Checkboxes);
        $fieldset->addField('checkboxes', 'category_checkboxes',
            array(
                'label'     => $helper->__('Categories'),
                'name'      => 'category_ids[]',
                'required'  => false,
                'class'     => 'validate-one-required-by-name',
                'values'    => Mage::helper('priceupdatemanager/category')->getCategoryList(),
                'onclick'   => 'PriceUpdater.updateCheckboxes(this);'
            )
        );

        $attribute = $helper->getAttribute();
        if (is_null($attribute)) {
            $fieldset->addField('note', 'note',
                array(
                    'label' => $helper->__('Undefined attribute'),
                    'text'  => $helper->__('You can choose attribute for additional filtering on the %s', $helper->getConfigLinkHtml())
                )
            );
        } else {
            $fieldset->addField('attribute-code', 'hidden', array(
                    'name'  => 'attribute[code]',
                    'value' => $attribute['code'],
                )
            );

            $fieldset->addField('attribute-option', 'select',
                array(
                    'name'      => 'attribute[option]',
                    'label'     => $helper->__($attribute['label']),
                    'title'     => $helper->__($attribute['label']),
                    'required'  => false,
                    'values'    => $attribute['options'],
                    'after_element_html' =>
                        '<small>' . $helper->__('You can change this filter on the %s', $helper->getConfigLinkHtml()) . '</small>',
                )
            );
        }

        $fieldset->addField('action_type', 'select',
            array(
                'name'      => 'action_type',
                'label'     => $helper->__('Change Price By'),
                'title'     => $helper->__('Change Price By'),
                'required'  => true,
                'values'    => array(
                    Magecom_PriceUpdateManager_Model_Priceupdate::UPDATE_ACTION_TYPE_BY_PERCENT => $helper->__('Percent'),
                    Magecom_PriceUpdateManager_Model_Priceupdate::UPDATE_ACTION_TYPE_BY_FIXED   => $helper->__('Flat amount'),
                ),
                'value'     => Magecom_PriceUpdateManager_Model_Priceupdate::UPDATE_ACTION_TYPE_BY_FIXED,
                'onchange'  => 'PriceUpdater.updateRateClass(this.value);'
            )
        );

        $fieldset->addField('rate', 'text',
            array(
                'name'     => 'rate',
                'label'    => $helper->__('Amount'),
                'title'    => $helper->__('Amount'),
                'required' => true,
                'class'    => 'validate-number',
                'after_element_html' => '<small id="value_comment">' . $helper->__('For example: 7.25 or -7.25') . '</small>'
            )
        );

        $fieldset->addField('priceupdate', 'button',
            array(
                'name'      => 'priceupdater',
                'value'     => $helper->__('Update'),
                'style'     => 'margin-left:20%; width:100px',
                'onclick'   => 'PriceUpdater.submit();'
            )
        );

        $fieldset->addField('category_url', 'hidden', array(
                'name'  => 'category_url',
                'value' => Mage::helper('adminhtml')->getUrl('adminhtml/priceupdate/categorylist'),
            )
        );

        return parent::_prepareForm();
    }
}