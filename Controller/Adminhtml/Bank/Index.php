<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Bank;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::bank';

    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('BrainStation23_EmiManagement::bank');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Banks'));

        return $resultPage;
    }
}
