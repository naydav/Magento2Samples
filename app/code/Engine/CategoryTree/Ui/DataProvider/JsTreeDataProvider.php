<?php
namespace Engine\CategoryTree\Ui\DataProvider;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\CategoryTree\Api\CategoryTreeLoaderInterface;
use Engine\CategoryTree\Api\Data\CategoryTreeInterface;
use Engine\JsTree\Ui\DataProvider\JsTreeDataBuilderInterface;
use Engine\JsTree\Ui\DataProvider\JsTreeDataInterface;
use Engine\JsTree\Ui\DataProvider\JsTreeDataProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class JsTreeDataProvider implements JsTreeDataProviderInterface
{
    /**#@+
     * Constants defined for keys of items data array
     */
    const CUSTOM_ITEM_DATA_ADD_CHILD = 'add_child';
    const CUSTOM_ITEM_DATA_EDIT = 'edit';
    /**#@-*/

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var CategoryTreeLoaderInterface
     */
    private $categoryTreeLoader;

    /**
     * @var JsTreeDataBuilderInterface
     */
    private $jsTreeDataBuilder;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var string
     */
    private $treeId;

    /**
     * @var string
     */
    private $uiComponentName;

    /**
     * @param UrlInterface $urlBuilder
     * @param CategoryTreeLoaderInterface $categoryTreeLoader
     * @param JsTreeDataBuilderInterface $jsTreeDataBuilder
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
     * @param string $treeId
     * @param string $uiComponentName
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CategoryTreeLoaderInterface $categoryTreeLoader,
        JsTreeDataBuilderInterface $jsTreeDataBuilder,
        RootCategoryIdProviderInterface $rootCategoryIdProvider,
        $treeId = 'category-tree',
        $uiComponentName = 'EngineEntityTree'
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->categoryTreeLoader = $categoryTreeLoader;
        $this->jsTreeDataBuilder = $jsTreeDataBuilder;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
        $this->treeId = $treeId;
        $this->uiComponentName = $uiComponentName;
    }

    /**
     * {@inheritdoc}
     */
    public function provide($entityId = null, $withRoot = false)
    {
        $treeData = $this->getTreeData($entityId, $withRoot);

        $jsTreeData = $this->jsTreeDataBuilder->setTreeId($this->treeId)
            ->setIsNotEmpty(count($treeData) > 0)
            ->setJsComponentConfig($this->getJsComponentConfig($treeData))
            ->create();
        return $jsTreeData;
    }

    /**
     * @param int|null $entityId
     * @param bool $withRoot
     * @return array
     */
    private function getTreeData($entityId = null, $withRoot = false)
    {
        $tree = $this->categoryTreeLoader->getTree($entityId);
        $treeData = $this->buildTreeData($tree);

        if (false === $withRoot) {
            $treeData = $treeData[JsTreeDataInterface::ITEM_CHILDREN];
        }
        return $treeData;
    }

    /**
     * @param CategoryTreeInterface $tree
     * @return array
     */
    private function buildTreeData(CategoryTreeInterface $tree)
    {
        $treeData = $this->getItemData($tree);
        $children = $tree->getChildren();
        if ($children) {
            foreach ($children as $child) {
                $treeData[JsTreeDataInterface::ITEM_CHILDREN][] = $this->buildTreeData($child);
            }
        } else {
            $treeData[JsTreeDataInterface::ITEM_CHILDREN] = [];
        }
        return $treeData;
    }

    /**
     * @param CategoryTreeInterface $tree
     * @return array
     */
    private function getItemData(CategoryTreeInterface $tree)
    {
        $category = $tree->getCategory();
        $addChildUrl = $this->urlBuilder->getUrl('*/*/new', [
            CategoryInterface::PARENT_ID => $category->getCategoryId()
        ]);
        $editUrl = $this->urlBuilder->getUrl('*/*/edit', [
            CategoryInterface::CATEGORY_ID => $category->getCategoryId()
        ]);
        return [
            JsTreeDataInterface::ITEM_ID => $category->getCategoryId(),
            JsTreeDataInterface::ITEM_TEXT => $category->getTitle(),
            JsTreeDataInterface::ITEM_DATA => [
                CategoryInterface::CATEGORY_ID => $category->getCategoryId(),
                CategoryInterface::IS_ENABLED => $category->getIsEnabled()
                    ? sprintf('<span class="icon tick" title="%1$s">%1$s</span>', __('Yes')) : '',
                CategoryInterface::IS_ANCHOR => $category->getIsAnchor()
                    ? sprintf('<span class="icon tick" title="%1$s">%1$s</span>', __('Yes')) : '',
                self::CUSTOM_ITEM_DATA_ADD_CHILD => sprintf(
                    '<a href="%1$s" class="icon add" title="%2$s">%2$s</a>',
                    $addChildUrl,
                    __('Add Child')
                ),
                self::CUSTOM_ITEM_DATA_EDIT => sprintf(
                    '<a href="%1$s" class="icon edit" title="%2$s">%2$s</a>',
                    $editUrl,
                    __('Edit')
                ),
            ],
        ];
    }

    /**
     * @param array $treeData
     * @return array
     */
    private function getJsComponentConfig(array $treeData)
    {
        $jsComponentConfig = [
            $this->uiComponentName => [
                JsTreeDataInterface::BASE_ROOT_ID => $this->rootCategoryIdProvider->provide(),
                JsTreeDataInterface::MOVE_URL => $this->getMoveUrl(),
                JsTreeDataInterface::TREE_OPTIONS => [
                    JsTreeDataInterface::TREE_OPTIONS_GRID => [
                        JsTreeDataInterface::TREE_OPTIONS_GRID_COLUMNS => $this->getGridColumns(),
                    ],
                    JsTreeDataInterface::TREE_OPTIONS_CORE => [
                        JsTreeDataInterface::TREE_OPTIONS_CORE_DATA => $treeData,
                    ],
                ],
            ],
        ];
        return $jsComponentConfig;
    }

    /**
     * @return string
     */
    private function getMoveUrl()
    {
        $moveUrl = $this->urlBuilder->getUrl('engine_category/category/move');
        return $moveUrl;
    }

    /**
     * @return array
     */
    private function getGridColumns()
    {
        return [
            [
                JsTreeDataInterface::GRID_COLUMN_WIDTH => 1000,
                JsTreeDataInterface::GRID_COLUMN_HEADER => __('Title'),
            ],
            [
                JsTreeDataInterface::GRID_COLUMN_HEADER => __('ID'),
                JsTreeDataInterface::GRID_COLUMN_VALUE => CategoryInterface::CATEGORY_ID,
                JsTreeDataInterface::GRID_COLUMN_CELL_CLASS => 'engine-jstree-cell-text',
            ],
            [
                JsTreeDataInterface::GRID_COLUMN_HEADER => __('Enabled'),
                JsTreeDataInterface::GRID_COLUMN_VALUE => CategoryInterface::IS_ENABLED,
                JsTreeDataInterface::GRID_COLUMN_CELL_CLASS => 'engine-jstree-cell-icon',
            ],
            [
                JsTreeDataInterface::GRID_COLUMN_HEADER => __('Anchor'),
                JsTreeDataInterface::GRID_COLUMN_VALUE => CategoryInterface::IS_ANCHOR,
                JsTreeDataInterface::GRID_COLUMN_CELL_CLASS => 'engine-jstree-cell-icon',
            ],
            [
                JsTreeDataInterface::GRID_COLUMN_HEADER => __('Add Sub.'),
                JsTreeDataInterface::GRID_COLUMN_VALUE => self::CUSTOM_ITEM_DATA_ADD_CHILD,
                JsTreeDataInterface::GRID_COLUMN_CELL_CLASS => 'engine-jstree-cell-icon',
            ],
            [
                JsTreeDataInterface::GRID_COLUMN_HEADER => __('Edit'),
                JsTreeDataInterface::GRID_COLUMN_VALUE => self::CUSTOM_ITEM_DATA_EDIT,
                JsTreeDataInterface::GRID_COLUMN_CELL_CLASS => 'engine-jstree-cell-icon',
            ],
        ];
    }
}
