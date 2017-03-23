<?php
namespace Engine\Category\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface RootCategoryIdProviderInterface
{
    /**
     * @return int
     */
    public function get();
}
