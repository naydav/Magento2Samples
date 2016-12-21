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
    public function create(array $items, $size, SearchCriteriaInterface $searchCriteria, $idFieldName)
    {
        $documents = [];
        foreach ($items as $item) {
            $itemData = $this->hydrator->extract($item);
            $itemId = $itemData[$idFieldName];

            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode('id_field_name');
            $attribute->setValue($idFieldName);
            $attributes[] = $attribute;
            foreach ($itemData as $key => $value) {
                $attribute = $this->attributeValueFactory->create();
                $attribute->setAttributeCode($key);
                if (is_bool($value)) {
                    // for proper work form and grid
                    $value = (string)(int)$value;
                }
                $attribute->setValue($value);
                $attributes[] = $attribute;
            }

            $document = $this->documentFactory->create();
            $document->setId($itemId);
            $document->setCustomAttributes($attributes);
            $documents[] = $document;
        }

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setItems($documents);
        $searchResult->setTotalCount($size);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }
}
