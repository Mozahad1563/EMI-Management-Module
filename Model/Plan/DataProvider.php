<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Model\Plan;

use BrainStation23\EmiManagement\Model\ResourceModel\Plan\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private ?array $loadedData = null;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $items = $this->collection->getItems();

        foreach ($items as $plan) {
            $this->loadedData[$plan->getId()] = $plan->getData();
        }

        $persistedData = $this->dataPersistor->get('emi_plan');

        if (!empty($persistedData)) {
            $plan = $this->collection->getNewEmptyItem();
            $plan->setData($persistedData);
            $this->loadedData[$plan->getId()] = $plan->getData();
            $this->dataPersistor->clear('emi_plan');
        }

        return $this->loadedData;
    }
}
