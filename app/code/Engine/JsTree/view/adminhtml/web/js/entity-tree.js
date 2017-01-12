/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'jquery',
    'jquery/ui',
    'Engine_JsTree/js/jstree',
    'Engine_JsTree/js/jstreegrid'
], function ($) {
    'use strict';

    $.widget('engine.entityTree', {
        options: {
            treeSekector: '.entity-tree',
            withSearch: true,
            withDargAndDrop: true,
            moveUrl: null,
            searchInputClass: 'engine-jstree-tree-search-input',
            minSymbolsForSearch: 3,
            baseRootId: null,
            treeOptions: {
                plugins: [
                    'dnd',
                    'search',
                    'state',
                    'wholerow',
                    'grid'
                ],
                core: {
                    check_callback: true
                },
                search: {
                    show_only_matches: true
                },
                state: {
                    filter: function (k) {
                        delete k.core.selected;
                        return k;
                    }
                }
            }
        },
        tree: null,

        _create: function () {
            this._initTree();
            this._initSearch();
            this._initDragAndDrop();
        },

        _initTree: function () {
            this.tree = $(this.options.treeSekector, this.element);
            this.tree
                .jstree(this.options.treeOptions)
                .on('select_node.jstree', function (e, data) {
                    // data.event is need for prevent change location by saved in state a node selection
                    if (data.event && data.node.a_attr.href) {
                        window.location = data.node.a_attr.href;
                    }
                });
        },

        _initSearch: function () {
            if (this.options.withSearch) {
                var self = this,
                    searchInput = null,
                    to = false;

                searchInput = $('<input/>').attr({type: 'text', value: '', class: this.options.searchInputClass});
                searchInput.keyup(function () {
                    if (to) {
                        clearTimeout(to);
                    }
                    to = setTimeout(function () {
                        var v = searchInput.val();
                        if (v.length >= self.options.minSymbolsForSearch || 0 === v.length) {
                            self.tree.jstree(true).search(v);
                        }
                    }, 250);
                });
                this.element.prepend(searchInput);
            }
        },

        _initDragAndDrop: function () {
            if (this.options.withDargAndDrop) {
                var self = this;
                this.tree
                    .on('move_node.jstree', function (e, data) {
                        var id = data.node.id,
                            parentId = data.parent,
                            afterId = null;

                        if (parentId === '#') {
                            parentId = self.options.baseRootId ? self.options.baseRootId : null;
                        }
                        if (data.position !== 0) {
                            var children = self.tree.jstree(true).get_node(data.parent).children;
                            var position = children.indexOf(id);
                            afterId = children[position - 1];
                        }
                        $.ajax({
                            url: self.options.moveUrl,
                            dataType: 'json',
                            data: {
                                moveData: {
                                    id: id,
                                    parentId: parentId,
                                    afterId: afterId
                                },
                                form_key: FORM_KEY
                            },
                            success: function (resp) {
                                self._clearNotifyMessage();
                                resp.messages.forEach(function (item, i, arr) {
                                    self._notifyMessage(!!resp.error, item);
                                });
                            },
                            showLoader: true,
                            context: $('body')
                        });
                    });
            }
        },

        _clearNotifyMessage: function (isError, message) {
            $('body').notification('clear');
        },

        _notifyMessage: function (isError, message) {
            $('body').notification('clear').notification('add', {
                error: isError,
                message: message,
                insertMethod: function (message) {
                    $('.page-main-actions').after(message);
                }
            });
        }
    });
    return $.engine.entityTree;
});
