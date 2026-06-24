<?php
/**
 * Brain Station 23
 *
 * @category   BrainStation23
 * @package    EmiManagement
 * @author     Brain Station 23
 * @copyright  Copyright (c) 2026 Brain Station 23
 */

declare(strict_types=1);

namespace BrainStation23\EmiManagement\Model;

use BrainStation23\EmiManagement\Model\ResourceModel\ProductEmi as ProductEmiResource;
use Magento\Framework\Model\AbstractModel;

class ProductEmi extends AbstractModel
{
    protected $_eventPrefix = 'product_emi';

    protected function _construct(): void
    {
        $this->_init(ProductEmiResource::class);
    }
}
