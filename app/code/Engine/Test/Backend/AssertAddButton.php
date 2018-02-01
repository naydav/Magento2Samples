<?php
declare(strict_types=1);

namespace Engine\Test\Backend;

use PHPUnit\Framework\DOMTestCase;

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
        DOMTestCase::assertSelectCount(
            "[data-ui-id=page-actions-toolbar-content-header] [data-ui-id={$uiId}]",
            1,
            $html,
            'Add new button is missed'
        );
    }
}
