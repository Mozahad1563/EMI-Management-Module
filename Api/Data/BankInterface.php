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

interface BankInterface
{
    public const TABLE_NAME = 'emi_bank';
    public const ID = 'id';
    public const NAME = 'name';
    public const LOGO = 'logo';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

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
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * @return string|null
     */
    public function getLogo(): ?string;

    /**
     * @param string|null $logo
     * @return $this
     */
    public function setLogo(?string $logo): self;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;
}
