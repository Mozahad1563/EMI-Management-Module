<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Plan;

use BrainStation23\EmiManagement\Api\PlanRepositoryInterface;
use BrainStation23\EmiManagement\Model\PlanFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::plan';

    public function __construct(
        Action\Context $context,
        private readonly PlanRepositoryInterface $planRepository,
        private readonly PlanFactory $planFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $id = (int) ($data['id'] ?? 0);

        try {
            $plan = $id ? $this->planRepository->getById($id) : $this->planFactory->create();

            $plan->setBankId((int) $data['bank_id']);
            $plan->setMonths((int) $data['months']);
            $plan->setFeePercentage((float) $data['fee_percentage']);
            $plan->setStatus((int) ($data['status'] ?? 1));

            $this->planRepository->save($plan);
            $this->messageManager->addSuccessMessage(__('The EMI plan has been saved.'));

            if ($this->getRequest()->getParam('back') === 'edit') {
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/edit',
                    ['id' => $plan->getId()]
                );
            }

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the plan.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $id]);
    }
}
