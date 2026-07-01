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

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Bank;

use BrainStation23\EmiManagement\Api\BankRepositoryInterface;
use BrainStation23\EmiManagement\Api\PlanRepositoryInterface;
use BrainStation23\EmiManagement\Model\BankFactory;
use BrainStation23\EmiManagement\Model\PlanFactory;
use BrainStation23\EmiManagement\Model\ResourceModel\Plan as PlanResource;
use BrainStation23\EmiManagement\Model\ResourceModel\Plan\CollectionFactory as PlanCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::bank';

    /**
     * @param Action\Context $context
     * @param BankRepositoryInterface $bankRepository
     * @param BankFactory $bankFactory
     * @param ImageUploader $imageUploader
     * @param PlanRepositoryInterface $planRepository
     * @param PlanFactory $planFactory
     * @param PlanCollectionFactory $planCollectionFactory
     * @param PlanResource $planResource
     */
    public function __construct(
        Action\Context $context,
        private readonly BankRepositoryInterface $bankRepository,
        private readonly BankFactory $bankFactory,
        private readonly ImageUploader $imageUploader,
        private readonly PlanRepositoryInterface $planRepository,
        private readonly PlanFactory $planFactory,
        private readonly PlanCollectionFactory $planCollectionFactory,
        private readonly PlanResource $planResource
    ) {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $id = (int) ($data['id'] ?? 0);

        try {
            $bank = $id ? $this->bankRepository->getById($id) : $this->bankFactory->create();

            if (isset($data['logo']) && is_array($data['logo'])) {
                if (!empty($data['logo'][0]['name']) && !empty($data['logo'][0]['tmp_name'])) {
                    $data['logo'] = $data['logo'][0]['name'];
                    $this->imageUploader->moveFileFromTmp($data['logo']);
                } elseif (!empty($data['logo'][0]['name'])) {
                    $data['logo'] = $data['logo'][0]['name'];
                } else {
                    $data['logo'] = null;
                }
            } elseif (empty($data['logo'])) {
                $data['logo'] = null;
            }

            $bank->setName($data['name']);
            $bank->setLogo($data['logo'] ?? null);
            $bank->setStatus((int) ($data['status'] ?? 1));

            $this->bankRepository->save($bank);

            // Sync tenure plans
            if (isset($data['tenure_plans']) && is_array($data['tenure_plans'])) {
                $processedIds = [];
                foreach ($data['tenure_plans'] as $row) {
                    if (empty($row['months'])) {
                        continue;
                    }

                    $plan = $this->planFactory->create();
                    $planId = isset($row['id']) && is_numeric($row['id']) ? (int)$row['id'] : null;
                    if ($planId) {
                        $this->planResource->load($plan, $planId);
                    }

                    $plan->setBankId((int)$bank->getId());
                    $plan->setMonths((int)$row['months']);
                    $plan->setFeePercentage((float)$row['fee_percentage']);
                    $plan->setStatus((int)($row['status'] ?? 1));

                    $this->planRepository->save($plan);
                    $processedIds[] = $plan->getId();
                }

                // Delete removed plans
                $plansCollection = $this->planCollectionFactory->create();
                $plansCollection->addFieldToFilter('bank_id', $bank->getId());
                if (!empty($processedIds)) {
                    $plansCollection->addFieldToFilter('id', ['notin' => $processedIds]);
                }
                foreach ($plansCollection as $oldPlan) {
                    $this->planRepository->delete($oldPlan);
                }
            } else {
                // Delete all existing plans for this bank
                $plansCollection = $this->planCollectionFactory->create();
                $plansCollection->addFieldToFilter('bank_id', $bank->getId());
                foreach ($plansCollection as $oldPlan) {
                    $this->planRepository->delete($oldPlan);
                }
            }

            $this->messageManager->addSuccessMessage(__('The bank and its tenures have been saved.'));

            if ($this->getRequest()->getParam('back') === 'edit') {
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/edit',
                    ['id' => $bank->getId()]
                );
            }

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the bank.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $id]);
    }
}
