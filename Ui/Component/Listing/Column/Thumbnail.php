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

namespace BrainStation23\EmiManagement\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    public const ALT_FIELD = 'name';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['logo']) && $item['logo'] !== '') {
                    $logoUrl = $mediaUrl . 'emi/logos/' . $item['logo'];
                    $item[$fieldName . '_src'] = $logoUrl;
                    $item[$fieldName . '_orig_src'] = $logoUrl;
                    $item[$fieldName . '_alt'] = $this->getAlt($item);
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'emi/bank/edit',
                        ['id' => $item['entity_id']]
                    );
                } else {
                    $item[$fieldName . '_src'] = '';
                    $item[$fieldName . '_orig_src'] = '';
                    $item[$fieldName . '_alt'] = '';
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get Alt
     *
     * @param array $row
     * @return string|null
     */
    protected function getAlt(array $row): ?string
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        if (!isset($row[$altField])) {
            return null;
        }
        return html_entity_decode((string)$row[$altField], ENT_QUOTES, "UTF-8");
    }
}
