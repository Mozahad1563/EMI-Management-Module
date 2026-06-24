<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Plan;

use BrainStation23\EmiManagement\Api\PlanRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::plan';

    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly PlanRepositoryInterface $planRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $title = __('New EMI Plan');

        if ($id) {
            try {
                $plan = $this->planRepository->getById($id);
                $title = __('Edit EMI Plan #%1', $plan->getId());
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This EMI plan no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('BrainStation23_EmiManagement::plan');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
