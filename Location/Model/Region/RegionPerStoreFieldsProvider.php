<?php
namespace Engine\Location\Model\Region;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionPerStoreFieldsProvider
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
