<?php
namespace Engine\Category\Model\Category\ResourceModel;

use Engine\Category\Api\Data\CategoryInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryResource extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('engine_category', CategoryInterface::CATEGORY_ID);
    }
}
