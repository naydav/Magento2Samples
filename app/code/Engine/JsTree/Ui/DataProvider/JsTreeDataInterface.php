<?php
namespace Engine\JsTree\Ui\DataProvider;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface JsTreeDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const TREE_ID = 'treeId';
    const IS_NOT_EMPTY = 'isNotEmpty';
    const JS_COMPONENT_CONFIG = 'jsComponentConfig';
    /**#@-*/

    /**#@+
     * Constants defined for js config array
     */
    const MOVE_URL = 'moveUrl';
    const BASE_ROOT_ID = 'baseRootId';
    const TREE_OPTIONS = 'treeOptions';
    const TREE_OPTIONS_CORE = 'core';
    const TREE_OPTIONS_CORE_DATA = 'data';
    const TREE_OPTIONS_GRID = 'grid';
    const TREE_OPTIONS_GRID_COLUMNS = 'columns';
    /**#@-*/

    /**#@+
     * Constants defined for keys of items data array
     */
    const ITEM_ID = 'id';
    const ITEM_TEXT = 'text';
    const ITEM_A_ATTR = 'a_attr';
    const ITEM_A_ATTR_HREF = 'href';
    const ITEM_CHILDREN = 'children';
    const ITEM_DATA = 'data';
    /**#@-*/

    /**#@+
     * Constants defined for keys of grid columns array
     */
    const GRID_COLUMN_WIDTH = 'width';
    const GRID_COLUMN_HEADER = 'header';
    const GRID_COLUMN_VALUE = 'value';
    const GRID_COLUMN_HEADER_CLASS = 'headerClass';
    const GRID_COLUMN_CELL_CLASS = 'wideCellClass';
    /**#@-*/

    /**
     * @return string
     */
    public function getTreeId();

    /**
     * @return bool
     */
    public function isNotEmpty();

    /**
     * @return string
     */
    public function getJsComponentConfig();
}
