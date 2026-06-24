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

use BrainStation23\EmiManagement\Model\ResourceModel\Bank\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class BankOptions implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('status', Status::ACTIVE);

            foreach ($collection as $bank) {
                $this->options[] = [
                    'value' => $bank->getId(),
                    'label' => $bank->getName(),
                ];
            }
        }

        return $this->options;
    }
}
