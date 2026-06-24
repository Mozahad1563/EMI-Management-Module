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

use BrainStation23\EmiManagement\Api\Data\BankInterface;
use BrainStation23\EmiManagement\Model\ResourceModel\Bank as BankResource;
use Magento\Framework\Model\AbstractModel;

class Bank extends AbstractModel implements BankInterface
{
    protected $_eventPrefix = 'emi_bank';

    protected function _construct(): void
    {
        $this->_init(BankResource::class);
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): BankInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getLogo(): ?string
    {
        return $this->getData(self::LOGO);
    }

    public function setLogo(?string $logo): BankInterface
    {
        return $this->setData(self::LOGO, $logo);
    }

    public function getStatus(): int
    {
        return (int) $this->getData(self::STATUS);
    }

    public function setStatus(int $status): BankInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }
}
