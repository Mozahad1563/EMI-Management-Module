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

namespace BrainStation23\EmiManagement\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use BrainStation23\MobileAuthoring\Api\BankEmiRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;

class EmiManagement extends Template
{
    private Registry $registry;
    private BankEmiRepositoryInterface $bankEmiRepository;
    private Json $jsonSerializer;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param BankEmiRepositoryInterface $bankEmiRepository
     * @param Json $jsonSerializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        BankEmiRepositoryInterface $bankEmiRepository,
        Json $jsonSerializer,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->bankEmiRepository = $bankEmiRepository;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context, $data);
    }

    /**
     * Get current product from registry
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get product price
     *
     * @return float
     */
    public function getProductPrice(): float
    {
        $product = $this->getProduct();
        if ($product) {
            return (float) $product->getFinalPrice();
        }
        return 0.0;
    }

    /**
     * Check if product is EMI eligible
     *
     * @return bool
     */
    public function isEmiEligible(): bool
    {
        $product = $this->getProduct();
        if ($product) {
            // Check attribute product_has_emi (1 = enabled, 0 = disabled)
            $hasEmi = $product->getData('product_has_emi');
            if ($hasEmi !== null) {
                return (int)$hasEmi === 1;
            }
        }
        return true;
    }

    /**
     * Get Bank Configuration Data JSON
     *
     * @return string
     */
    public function getBankDataJson(): string
    {
        $bankData = [];
        try {
            $details = $this->bankEmiRepository->getDetails();
            if (!empty($details)) {
                foreach ($details as $detail) {
                    $tenures = [];
                    foreach ($detail->getTenures() as $tenure) {
                        $tenures[] = [
                            'months' => (int) $tenure->getTenureMonths(),
                            'interest_rate' => round($tenure->getInterestRate() / 100, 4)
                        ];
                    }
                    $bankId = $detail->getBankId();
                    
                    // Assign min_amount based on bank requirements or bank name
                    $minAmount = 5000;
                    if ($bankId === 'ebl' || stripos($detail->getBankName(), 'Eastern Bank') !== false) {
                        $minAmount = 10000;
                    }
                    
                    $bankData[] = [
                        'bank_id' => $bankId,
                        'bank_name' => $detail->getBankName(),
                        'logo_url' => $detail->getLogoUrl() ?: '',
                        'min_amount' => $minAmount,
                        'tenures' => $tenures
                    ];
                }
            }
        } catch (\Exception $e) {
            // Fallback to mock data if repository fails or not populated
        }

        // If no bank data was fetched from repository, fall back to mock data specified in the requirements
        if (empty($bankData)) {
            $bankData = [
                [
                    'bank_id' => 'city_bank',
                    'bank_name' => 'City Bank',
                    'logo_url' => '',
                    'min_amount' => 5000,
                    'tenures' => [
                        ['months' => 3, 'interest_rate' => 0.00],
                        ['months' => 6, 'interest_rate' => 0.00],
                        ['months' => 12, 'interest_rate' => 0.04]
                    ]
                ],
                [
                    'bank_id' => 'ebl',
                    'bank_name' => 'Eastern Bank PLC (EBL)',
                    'logo_url' => '',
                    'min_amount' => 10000,
                    'tenures' => [
                        ['months' => 3, 'interest_rate' => 0.00],
                        ['months' => 6, 'interest_rate' => 0.00],
                        ['months' => 12, 'interest_rate' => 0.00],
                        ['months' => 18, 'interest_rate' => 0.06]
                    ]
                ]
            ];
        }

        return $this->jsonSerializer->serialize($bankData);
    }
}
