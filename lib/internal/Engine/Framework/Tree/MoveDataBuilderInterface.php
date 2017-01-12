<?php
namespace Engine\Framework\Tree;

use Magento\Framework\Api\SimpleBuilderInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @method MoveDataInterface create()
 * @api
 */
interface MoveDataBuilderInterface extends SimpleBuilderInterface
{
    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * @param int $afterId
     * @return $this
     */
    public function setAfterId($afterId);
}
