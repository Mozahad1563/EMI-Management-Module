<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Model\Bank;

use BrainStation23\EmiManagement\Model\ResourceModel\Bank\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
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
        private readonly StoreManagerInterface $storeManager,
        private readonly \BrainStation23\EmiManagement\Model\ResourceModel\Plan\CollectionFactory $planCollectionFactory,
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

        foreach ($items as $bank) {
            $data = $bank->getData();

            if (!empty($data['logo'])) {
                $mediaUrl = $this->storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                $data['logo'] = [
                    [
                        'name' => $data['logo'],
                        'url' => $mediaUrl . 'emi/logos/' . $data['logo'],
                    ],
                ];
            }

            // Load associated plans/tenures
            $plansCollection = $this->planCollectionFactory->create();
            $plansCollection->addFieldToFilter('bank_id', $bank->getId());
            $plans = [];
            foreach ($plansCollection as $plan) {
                $plans[] = [
                    'id' => $plan->getId(),
                    'months' => $plan->getMonths(),
                    'fee_percentage' => $plan->getFeePercentage(),
                    'status' => $plan->getStatus(),
                ];
            }
            $data['tenure_plans'] = $plans;

            $this->loadedData[$bank->getId()] = $data;
        }

        $persistedData = $this->dataPersistor->get('emi_bank');

        if (!empty($persistedData)) {
            $bank = $this->collection->getNewEmptyItem();
            $bank->setData($persistedData);
            $this->loadedData[$bank->getId()] = $bank->getData();
            $this->dataPersistor->clear('emi_bank');
        }

        return $this->loadedData;
    }
}
