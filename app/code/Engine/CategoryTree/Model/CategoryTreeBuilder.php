<?php
namespace Engine\CategoryTree\Model;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\CategoryTree\Api\Data\CategoryTreeInterface;
use Magento\Framework\Api\AbstractSimpleObjectBuilder;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @method CategoryTreeInterface create()
 */
class CategoryTreeBuilder extends AbstractSimpleObjectBuilder
{
    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->data[CategoryTreeInterface::ID] = $id;
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->data[CategoryTreeInterface::TITLE] = $title;
        return $this;
    }

    /**
     * @param \Engine\Category\Api\Data\CategoryInterface $category
     * @return $this
     */
    public function setCategory(CategoryInterface $category)
    {
        $this->data[CategoryTreeInterface::CATEGORY] = $category;
        return $this;
    }

    /**
     * @param CategoryTreeInterface[] $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        $this->data[CategoryTreeInterface::CHILDREN] = $children;
        return $this;
    }
}
