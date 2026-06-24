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

namespace BrainStation23\EmiManagement\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public const ACTIVE = 1;
    public const INACTIVE = 0;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::ACTIVE, 'label' => __('Active')],
            ['value' => self::INACTIVE, 'label' => __('Inactive')],
        ];
    }
}
