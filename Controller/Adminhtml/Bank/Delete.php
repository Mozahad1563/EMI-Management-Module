<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Bank;

use BrainStation23\EmiManagement\Api\BankRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::bank';

    public function __construct(
        Action\Context $context,
        private readonly BankRepositoryInterface $bankRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->bankRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The bank has been deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
