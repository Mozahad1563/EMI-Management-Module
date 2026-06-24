<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Bank;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Upload extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::bank';

    public function __construct(
        Action\Context $context,
        private readonly ImageUploader $imageUploader
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $imageId = $this->getRequest()->getParam('param_name', 'logo');

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
