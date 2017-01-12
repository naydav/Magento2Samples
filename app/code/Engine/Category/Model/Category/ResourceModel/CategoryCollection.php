<?php
namespace Engine\Category\Model\Category\ResourceModel;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\Category;
use Engine\PerStoreDataSupport\Model\ResourceModel\AbstractCollection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryCollection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Category::class, CategoryResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaceName()
    {
        return CategoryInterface::class;
    }
}
