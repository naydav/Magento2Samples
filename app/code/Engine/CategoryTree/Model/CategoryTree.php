<?php
namespace Engine\CategoryTree\Model;

use Engine\CategoryTree\Api\Data\CategoryTreeInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryTree extends AbstractSimpleObject implements CategoryTreeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->_get(self::CHILDREN);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->_get(self::CATEGORY);
    }
}
