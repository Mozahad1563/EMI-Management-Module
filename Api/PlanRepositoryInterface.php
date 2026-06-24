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

namespace BrainStation23\EmiManagement\Api;

use BrainStation23\EmiManagement\Api\Data\PlanInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface PlanRepositoryInterface
{
    /**
     * @param int $id
     * @return PlanInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): PlanInterface;

    /**
     * @param PlanInterface $plan
     * @return PlanInterface
     * @throws CouldNotSaveException
     */
    public function save(PlanInterface $plan): PlanInterface;

    /**
     * @param PlanInterface $plan
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(PlanInterface $plan): bool;

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id): bool;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
