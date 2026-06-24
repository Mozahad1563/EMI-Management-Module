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

namespace BrainStation23\EmiManagement\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductEmi extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('product_emi', 'id');
    }
}
