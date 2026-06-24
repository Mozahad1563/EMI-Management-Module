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

use BrainStation23\EmiManagement\Api\EmiDataProviderInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class EmiManagement extends Template
{
    private ?ProductInterface $product = null;
    private bool $productLoaded = false;

    public function __construct(
        Template\Context $context,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly EmiDataProviderInterface $emiDataProvider,
        private readonly Json $jsonSerializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getProduct(): ?ProductInterface
    {
        if (!$this->productLoaded) {
            $this->productLoaded = true;
            $productId = (int) $this->getRequest()->getParam('id');

            if ($productId) {
                try {
                    $this->product = $this->productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                    $this->product = null;
                }
            }
        }

        return $this->product;
    }

    public function getProductPrice(): float
    {
        $product = $this->getProduct();

        return $product ? (float) $product->getFinalPrice() : 0.0;
    }

    public function isEmiEligible(): bool
    {
        $product = $this->getProduct();

        if ($product) {
            $hasEmi = $product->getData('product_has_emi');

            if ($hasEmi !== null) {
                return (int) $hasEmi === 1;
            }
        }

        return true;
    }

    public function getWidgetConfigJson(): string
    {
        $product = $this->getProduct();
        $bankData = $this->emiDataProvider->getBankData();

        if ($product) {
            $applicableBanks = $product->getData('emi_applicable_banks');

            if (!empty($applicableBanks)) {
                $allowedIds = array_map('trim', explode(',', (string) $applicableBanks));
                $bankData = array_values(array_filter($bankData, function (array $bank) use ($allowedIds) {
                    return in_array($bank['bank_id'], $allowedIds);
                }));
            }
        }

        return $this->jsonSerializer->serialize([
            'productPrice' => $this->getProductPrice(),
            'productName' => $product ? $product->getName() : '',
            'bankData' => $bankData,
        ]);
    }
}
