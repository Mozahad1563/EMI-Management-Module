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

use BrainStation23\EmiManagement\Api\PlanRepositoryInterface;
use BrainStation23\EmiManagement\Api\Data\PlanInterface;
use BrainStation23\EmiManagement\Model\ResourceModel\Plan as PlanResource;
use BrainStation23\EmiManagement\Model\ResourceModel\Plan\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PlanRepository implements PlanRepositoryInterface
{
    public function __construct(
        private readonly PlanResource $resource,
        private readonly PlanFactory $planFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    public function getById(int $id): PlanInterface
    {
        $plan = $this->planFactory->create();
        $this->resource->load($plan, $id);

        if (!$plan->getId()) {
            throw new NoSuchEntityException(__('EMI Plan with ID "%1" does not exist.', $id));
        }

        return $plan;
    }

    public function save(PlanInterface $plan): PlanInterface
    {
        try {
            $this->resource->save($plan);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save EMI plan: %1', $e->getMessage()), $e);
        }

        return $plan;
    }

    public function delete(PlanInterface $plan): bool
    {
        try {
            $this->resource->delete($plan);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete EMI plan: %1', $e->getMessage()), $e);
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
