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

use BrainStation23\EmiManagement\Api\Data\BankInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface BankRepositoryInterface
{
    /**
     * @param int $id
     * @return BankInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): BankInterface;

    /**
     * @param BankInterface $bank
     * @return BankInterface
     * @throws CouldNotSaveException
     */
    public function save(BankInterface $bank): BankInterface;

    /**
     * @param BankInterface $bank
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BankInterface $bank): bool;

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
