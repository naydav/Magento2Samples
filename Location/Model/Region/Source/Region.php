<?php
namespace Engine\Location\Model\Region\Source;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollection;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Region implements OptionSourceInterface
{
    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var null|array
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
            /** @var RegionCollection $regionCollection */
            $regionCollection = $this->regionCollectionFactory->create();
            $regionCollection->addStoreData();
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
