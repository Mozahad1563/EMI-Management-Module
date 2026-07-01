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

namespace BrainStation23\EmiManagement\Block\Adminhtml\Bank\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get Delete Button Data
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        $bankId = $this->getBankId();
        if ($bankId) {
            $data = [
                'label' => __('Delete Bank'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getUrl('*/*/delete', ['id' => $bankId]) . '\', {"data": {}})',
                'sort_order' => 20
            ];
        }
        return $data;
    }
}
