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

use BrainStation23\EmiManagement\Api\EmiDataProviderInterface;
use BrainStation23\EmiManagement\Model\ResourceModel\Bank\CollectionFactory as BankCollectionFactory;
use BrainStation23\EmiManagement\Model\ResourceModel\Plan\CollectionFactory as PlanCollectionFactory;
use BrainStation23\EmiManagement\Model\Source\Status;
use Magento\Store\Model\StoreManagerInterface;

class EmiDataProvider implements EmiDataProviderInterface
{
    public function __construct(
        private readonly BankCollectionFactory $bankCollectionFactory,
        private readonly PlanCollectionFactory $planCollectionFactory,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getBankData(): array
    {
        $banks = $this->bankCollectionFactory->create()
            ->addFieldToFilter('status', Status::ACTIVE);

        if ($banks->getSize() === 0) {
            return [];
        }

        $bankIds = $banks->getColumnValues('id');

        $plans = $this->planCollectionFactory->create()
            ->addFieldToFilter('bank_id', ['in' => $bankIds])
            ->addFieldToFilter('status', Status::ACTIVE)
            ->setOrder('months', 'ASC');

        $plansByBank = [];

        foreach ($plans as $plan) {
            $plansByBank[$plan->getBankId()][] = [
                'months' => $plan->getMonths(),
                'interest_rate' => round((float) $plan->getFeePercentage() / 100, 4),
            ];
        }

        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $result = [];

        foreach ($banks as $bank) {
            $bankId = (int) $bank->getId();

            if (empty($plansByBank[$bankId])) {
                continue;
            }

            $logoUrl = '';

            if ($bank->getLogo()) {
                $logoUrl = $mediaUrl . 'emi/logos/' . $bank->getLogo();
            }

            $result[] = [
                'bank_id' => (string) $bankId,
                'bank_name' => $bank->getName(),
                'logo_url' => $logoUrl,
                'min_amount' => 5000,
                'tenures' => $plansByBank[$bankId],
            ];
        }

        return $result;
    }
}
