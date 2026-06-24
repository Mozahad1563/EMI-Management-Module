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

use BrainStation23\EmiManagement\Api\Data\PlanInterface;
use BrainStation23\EmiManagement\Model\ResourceModel\Plan as PlanResource;
use Magento\Framework\Model\AbstractModel;

class Plan extends AbstractModel implements PlanInterface
{
    protected $_eventPrefix = 'emi_plan';

    protected function _construct(): void
    {
        $this->_init(PlanResource::class);
    }

    public function getBankId(): ?int
    {
        $value = $this->getData(self::BANK_ID);

        return $value !== null ? (int) $value : null;
    }

    public function setBankId(int $bankId): PlanInterface
    {
        return $this->setData(self::BANK_ID, $bankId);
    }

    public function getMonths(): ?int
    {
        $value = $this->getData(self::MONTHS);

        return $value !== null ? (int) $value : null;
    }

    public function setMonths(int $months): PlanInterface
    {
        return $this->setData(self::MONTHS, $months);
    }

    public function getFeePercentage(): ?float
    {
        $value = $this->getData(self::FEE_PERCENTAGE);

        return $value !== null ? (float) $value : null;
    }

    public function setFeePercentage(float $feePercentage): PlanInterface
    {
        return $this->setData(self::FEE_PERCENTAGE, $feePercentage);
    }

    public function getStatus(): int
    {
        return (int) $this->getData(self::STATUS);
    }

    public function setStatus(int $status): PlanInterface
    {
        return $this->setData(self::STATUS, $status);
    }
}
