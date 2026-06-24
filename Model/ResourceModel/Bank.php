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

use BrainStation23\EmiManagement\Api\Data\BankInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Bank extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(BankInterface::TABLE_NAME, BankInterface::ID);
    }
}
