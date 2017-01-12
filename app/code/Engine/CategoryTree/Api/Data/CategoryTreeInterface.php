<?php
namespace Engine\CategoryTree\Api\Data;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CategoryTreeInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const TITLE = 'title';
    const CHILDREN = 'children';
    const CATEGORY = 'category';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return \Engine\CategoryTree\Api\Data\CategoryTreeInterface[]
     */
    public function getChildren();

    /**
     * @return \Engine\Category\Api\Data\CategoryInterface
     */
    public function getCategory();
}
