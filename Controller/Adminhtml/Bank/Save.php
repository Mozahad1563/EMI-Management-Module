<?php
declare(strict_types=1);

namespace BrainStation23\EmiManagement\Controller\Adminhtml\Bank;

use BrainStation23\EmiManagement\Api\BankRepositoryInterface;
use BrainStation23\EmiManagement\Model\BankFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'BrainStation23_EmiManagement::bank';

    public function __construct(
        Action\Context $context,
        private readonly BankRepositoryInterface $bankRepository,
        private readonly BankFactory $bankFactory,
        private readonly ImageUploader $imageUploader
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
            $this->messageManager->addSuccessMessage(__('The bank has been saved.'));

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
