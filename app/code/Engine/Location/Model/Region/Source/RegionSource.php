<?php
namespace Engine\Location\Model\Region\Source;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollection;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class RegionSource implements OptionSourceInterface
{
    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var array|null
     */
    private $data;

    /**
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(RegionCollectionFactory $regionCollectionFactory)
    {
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (null === $this->data) {
            $this->data = [];
            /** @var RegionCollection $regionCollection */
            $regionCollection = $this->regionCollectionFactory->create();
            $regionCollection->addStoreData()
                ->setOrder(RegionInterface::POSITION, RegionCollection::SORT_ORDER_ASC)
                ->addOrder(RegionInterface::REGION_ID, RegionCollection::SORT_ORDER_ASC);

            foreach ($regionCollection as $region) {
                /** @var RegionInterface $region */
                $this->data[] = [
                    'value' => $region->getRegionId(),
                    'label' => $region->getTitle(),
                ];
            }
        }
        return $this->data;
    }
}
