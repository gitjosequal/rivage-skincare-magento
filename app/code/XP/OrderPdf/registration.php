<?php
/**
 *  Prints Invoices and Order as per define configuration.
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'XP_OrderPdf', __DIR__);
