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

namespace BrainStation23\EmiManagement\Api\Data;

interface PlanInterface
{
    public const TABLE_NAME = 'emi_plan';
    public const ID = 'id';
    public const BANK_ID = 'bank_id';
    public const MONTHS = 'months';
    public const FEE_PERCENTAGE = 'fee_percentage';
    public const STATUS = 'status';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getBankId(): ?int;

    /**
     * @param int $bankId
     * @return $this
     */
    public function setBankId(int $bankId): self;

    /**
     * @return int|null
     */
    public function getMonths(): ?int;

    /**
     * @param int $months
     * @return $this
     */
    public function setMonths(int $months): self;

    /**
     * @return float|null
     */
    public function getFeePercentage(): ?float;

    /**
     * @param float $feePercentage
     * @return $this
     */
    public function setFeePercentage(float $feePercentage): self;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self;
}
