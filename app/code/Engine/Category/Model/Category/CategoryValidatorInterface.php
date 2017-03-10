<?php
namespace Engine\Category\Model\Category;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\MagentoFix\Exception\ValidatorException;

/**
 * Extension point for base validation
 *
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
 */
interface CategoryValidatorInterface
{
    /**
     * @param CategoryInterface $category
     * @return void
     * @throws ValidatorException
     */
    public function validate(CategoryInterface $category);
}
