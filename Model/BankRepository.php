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

namespace BrainStation23\EmiManagement\Model;

use BrainStation23\EmiManagement\Api\BankRepositoryInterface;
use BrainStation23\EmiManagement\Api\Data\BankInterface;
use BrainStation23\EmiManagement\Model\ResourceModel\Bank as BankResource;
use BrainStation23\EmiManagement\Model\ResourceModel\Bank\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BankRepository implements BankRepositoryInterface
{
    public function __construct(
        private readonly BankResource $resource,
        private readonly BankFactory $bankFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    public function getById(int $id): BankInterface
    {
        $bank = $this->bankFactory->create();
        $this->resource->load($bank, $id);

        if (!$bank->getId()) {
            throw new NoSuchEntityException(__('Bank with ID "%1" does not exist.', $id));
        }

        return $bank;
    }

    public function save(BankInterface $bank): BankInterface
    {
        try {
            $this->resource->save($bank);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save bank: %1', $e->getMessage()), $e);
        }

        return $bank;
    }

    public function delete(BankInterface $bank): bool
    {
        try {
            $this->resource->delete($bank);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete bank: %1', $e->getMessage()), $e);
        }

        return true;
    }

    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
