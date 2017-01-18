<?php
namespace Engine\Category\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CategoryInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CATEGORY_ID = 'category_id';
    const PARENT_ID = 'parent_id';
    const URL_KEY = 'url_key';
    const IS_ANCHOR = 'is_anchor';
    const IS_ENABLED = 'is_enabled';
    const POSITION = 'position';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    /**#@-*/

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $categoryId
     * @return void
     */
    public function setCategoryId($categoryId);

    /**
     * Return null for Root Category
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Value null is possible only for Root Category
     *
     * @param int|null $parentId
     * @return void
     */
    public function setParentId($parentId);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $urlKey
     * @return void
     */
    public function setUrlKey($urlKey);

    /**
     * @return bool
     */
    public function getIsAnchor();

    /**
     * @param bool $isAnchor
     * @return void
     */
    public function setIsAnchor($isAnchor);

    /**
     * @return bool
     */
    public function getIsEnabled();

    /**
     * @param bool $isEnabled
     * @return void
     */
    public function setIsEnabled($isEnabled);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     * @return void
     */
    public function setPosition($position);

    /**
     * Per store value

     * @return string
     */
    public function getTitle();

    /**
     * Per store value

     * @param string $title
     * @return void
     */
    public function setTitle($title);

    /**
     * Per store value

     * @return string
     */
    public function getDescription();

    /**
     * Per store value

     * @param string $description
     * @return void
     */
    public function setDescription($description);

    /**
     * @return \Engine\Category\Api\Data\CategoryExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Engine\Category\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(CategoryExtensionInterface $extensionAttributes);
}
