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
     * @param \Engine\Framework\Tree\MoveDataInterface $moveData
     * @return bool
     * @throws \Engine\Framework\Tree\CouldNotMoveException
     */
    public function move(\Engine\Framework\Tree\MoveDataInterface $moveData);
}
