<?php
namespace Engine\Tree\Api\Data;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface MoveDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const PARENT_ID = 'parentId';
    const AFTER_ID = 'afterId';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $parentId
     * @return void
     */
    public function setParentId($parentId);

    /**
     * @return int|null
     */
    public function getAfterId();

    /**
     * @param int|null $afterId
     * @return void
     */
    public function setAfterId($afterId);
}
