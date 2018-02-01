<?php
declare(strict_types=1);

namespace Engine\Location\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionSearchResultInterface;

/**
 * In Magento 2 Repository considered as an implementation of Facade pattern which provides a simplified interface
 * to a larger body of code responsible for Domain Entity management
 *
 * The main intention is to make API more readable and reduce dependencies of business logic code on the inner workings
 * of a module, since most code uses the facade, thus allowing more flexibility in developing the system
 *
 * Along with this such approach helps to segregate two responsibilities:
 * 1. Repository now could be considered as an API - Interface for usage (calling) in the business logic
 * 2. Separate class-commands to which Repository proxies initial call (like, Get Save GetList Delete) could be
 *    considered as SPI - Interfaces that you should extend and implement to customize current behaviour
 *
 * Used fully qualified namespaces in annotations for proper work of WebApi request parser
 *
 * @api
 * @author naydav <valeriy.nayda@gmail.com>
 */
interface RegionRepositoryInterface
{
    /**
     * Save Region data
     *
     * @param \Engine\Location\Api\Data\RegionInterface $region
     * @return int
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(RegionInterface $region): int;

    /**
     * Get Region data by given identifier
     *
     * If you want to create plugin on get method, also you need to create separate
     * plugin on getList method, because entity loading way is different for these methods
     *
     * @param int $regionId
     * @return \Engine\Location\Api\Data\RegionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $regionId): RegionInterface;

    /**
     * Find Regions by given SearchCriteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\Location\Api\Data\RegionSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): RegionSearchResultInterface;

    /**
     * Delete the Region data by identifier. If Region is not found do nothing
     *
     * @param int $regionId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $regionId);
}
