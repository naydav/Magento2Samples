<?php
declare(strict_types=1);

namespace Engine\Location\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CitySearchResultInterface;

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
interface CityRepositoryInterface
{
    /**
     * Save City data
     *
     * @param \Engine\Location\Api\Data\CityInterface $city
     * @return int
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CityInterface $city): int;

    /**
     * Get City data by given identifier
     *
     * If you want to create plugin on get method, also you need to create separate
     * plugin on getList method, because entity loading way is different for these methods
     *
     * @param int $cityId
     * @return \Engine\Location\Api\Data\CityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $cityId): CityInterface;

    /**
     * Find Cities by given SearchCriteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\Location\Api\Data\CitySearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CitySearchResultInterface;

    /**
     * Delete the City data by identifier. If City is not found do nothing
     *
     * @param int $cityId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $cityId);
}
