<?php
namespace Engine\Category\Model\Category;

use Engine\Category\Api\RootCategoryIdProviderInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RootCategoryIdProvider implements RootCategoryIdProviderInterface
{
    /**
     * Root category id
     */
    const ROOT_CATEGORY_D = 1;

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return self::ROOT_CATEGORY_D;
    }
}
