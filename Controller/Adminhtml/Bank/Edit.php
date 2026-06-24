<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Bank;

use BrainStation23\EmiManagement\Api\BankRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::bank';

    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly BankRepositoryInterface $bankRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $title = __('New Bank');

        if ($id) {
            try {
                $bank = $this->bankRepository->getById($id);
                $title = __('Edit Bank: %1', $bank->getName());
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This bank no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('BrainStation23_EmiManagement::bank');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
