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

namespace BrainStation23\EmiManagement\Model\ResourceModel\Bank;

use BrainStation23\EmiManagement\Model\Bank as BankModel;
use BrainStation23\EmiManagement\Model\ResourceModel\Bank as BankResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct(): void
    {
        $this->_init(BankModel::class, BankResource::class);
    }
}
