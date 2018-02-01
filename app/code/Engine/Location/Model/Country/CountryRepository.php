<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\Data\CountrySearchResultInterface;
use Engine\Location\Api\CountryRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class CountryRepository implements CountryRepositoryInterface
{
    /**
     * @var SaveCountryInterface
     */
    private $saveCountry;

    /**
     * @var GetCountryInterface
     */
    private $getCountry;

    /**
     * @var DeleteCountryByIdInterface
     */
    private $deleteCountryById;

    /**
     * @var GetCountryListInterface
     */
    private $getCountryList;

    /**
     * @param SaveCountryInterface $saveCountry
     * @param GetCountryInterface $getCountry
     * @param DeleteCountryByIdInterface $deleteCountryById
     * @param GetCountryListInterface $getCountryList
     */
    public function __construct(
        SaveCountryInterface $saveCountry,
        GetCountryInterface $getCountry,
        DeleteCountryByIdInterface $deleteCountryById,
        GetCountryListInterface $getCountryList
    ) {
        $this->saveCountry = $saveCountry;
        $this->getCountry = $getCountry;
        $this->deleteCountryById = $deleteCountryById;
        $this->getCountryList = $getCountryList;
    }

    /**
     * @inheritdoc
     */
    public function save(CountryInterface $country): int
    {
        return $this->saveCountry->execute($country);
    }

    /**
     * @inheritdoc
     */
    public function get(int $countryId): CountryInterface
    {
        return $this->getCountry->execute($countryId);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $countryId)
    {
        $this->deleteCountryById->execute($countryId);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CountrySearchResultInterface
    {
        return $this->getCountryList->execute($searchCriteria);
    }
}
