<?php
declare(strict_types=1);

class Mollie_Mpm_Model_Adminhtml_System_Config_Source_InvoiceMoment
{
    const ON_SHIPMENT = 'shipment';
    const ON_AUTHORIZE_PAID_BEFORE_SHIPMENT = 'authorize_paid_before_shipment';
    const ON_AUTHORIZE_PAID_AFTER_SHIPMENT = 'authorize_paid_after_shipment';

    public function toOptionArray()
    {
        return [
            [
                'value' => static::ON_AUTHORIZE_PAID_BEFORE_SHIPMENT,
                'label' => Mage::helper('mpm')->__('On Authorize and set status Paid before shipment'),
            ],
            [
                'value' => static::ON_AUTHORIZE_PAID_AFTER_SHIPMENT,
                'label' => Mage::helper('mpm')->__('On Authorize and set status Paid after shipment'),
            ],
            [
                'value' => static::ON_SHIPMENT,
                'label' => Mage::helper('mpm')->__('On Shipment'),
            ]
        ];
    }
}
