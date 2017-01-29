<?php
namespace Engine\PerStoreDataSupport\Ui\DataProvider;

use Engine\PerStoreDataSupport\Api\DataProviderSearchResultFactoryInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class DataProviderSearchResultFactory implements DataProviderSearchResultFactoryInterface
{
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @param HydratorInterface $hydrator
     * @param DocumentFactory $documentFactory
     * @param SearchResultFactory $searchResultFactory
     * @param AttributeValueFactory $attributeValueFactory
     */
    public function __construct(
        HydratorInterface $hydrator,
        DocumentFactory $documentFactory,
        SearchResultFactory $searchResultFactory,
        AttributeValueFactory $attributeValueFactory
    ) {
        $this->hydrator = $hydrator;
        $this->documentFactory = $documentFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $items, $totalCount, SearchCriteriaInterface $searchCriteria, $idFieldName)
    {
        $documents = [];
        foreach ($items as $item) {
            $itemData = $this->hydrator->extract($item);
            $itemId = $itemData[$idFieldName];
            $attributes = $this->createAttributes($idFieldName, $itemData);

            $document = $this->documentFactory->create();
            $document->setId($itemId);
            $document->setCustomAttributes($attributes);
            $documents[] = $document;
        }

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setItems($documents);
        $searchResult->setTotalCount($totalCount);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    /**
     * @param string $idFieldName
     * @param array $itemData
     * @return array
     */
    private function createAttributes($idFieldName, $itemData)
    {
        $attributes = [];

        $idFieldNameAttribute = $this->attributeValueFactory->create();
        $idFieldNameAttribute->setAttributeCode('id_field_name');
        $idFieldNameAttribute->setValue($idFieldName);
        $attributes[] = $idFieldNameAttribute;

        foreach ($itemData as $key => $value) {
            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode($key);
            if (is_bool($value)) {
                // for proper work form and grid (for example for Yes/No properties)
                $value = (string)(int)$value;
            }
            $attribute->setValue($value);
            $attributes[] = $attribute;
        }
        return $attributes;
    }
}
