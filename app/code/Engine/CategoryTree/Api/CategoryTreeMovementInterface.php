<?php
namespace Engine\CategoryTree\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CategoryTreeMovementInterface
{
    /**
     * If afterId parameter is missed then move on first position
     *
     * @param \Engine\Tree\Api\Data\MoveDataInterface $moveData
     * @return bool
     * @throws \Engine\Tree\Model\CouldNotMoveException
     */
    public function move(\Engine\Tree\Api\Data\MoveDataInterface $moveData);
}
