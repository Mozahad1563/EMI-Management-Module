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

namespace BrainStation23\EmiManagement\Api;

interface EmiDataProviderInterface
{
    /**
     * Get all active bank EMI data with tenure plans.
     *
     * @return array
     */
    public function getBankData(): array;
}
