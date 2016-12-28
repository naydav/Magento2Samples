<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertAddButton
{
    /**
     * @param string $html
     * @param string $uiId
     * @return void
     */
    public static function assert($html, $uiId)
    {
        \PHPUnit_Framework_Assert::assertSelectCount(
            "[data-ui-id=page-actions-toolbar-content-header] [data-ui-id={$uiId}]",
            1,
            $html,
            'Add new button is missed'
        );
    }
}
