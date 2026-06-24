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

namespace BrainStation23\EmiManagement\Model\Product\Attribute\Source;

use BrainStation23\EmiManagement\Model\ResourceModel\Bank\CollectionFactory;
use BrainStation23\EmiManagement\Model\Source\Status;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Banks extends AbstractSource
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [];
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('status', Status::ACTIVE);

            foreach ($collection as $bank) {
                $this->_options[] = [
                    'value' => $bank->getId(),
                    'label' => $bank->getName(),
                ];
            }
        }

        return $this->_options;
    }
}
