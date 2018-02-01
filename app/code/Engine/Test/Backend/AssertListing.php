<?php
declare(strict_types=1);

namespace Engine\Test\Backend;

use PHPUnit\Framework\DOMTestCase;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertListing
{
    /**
     * @param string $html
     * @param string $name
     * @return void
     */
    public static function assert($html, $name)
    {
        DOMTestCase::assertSelectCount(
            'main .admin__data-grid-outer-wrap',
            1,
            $html,
            'Grid is missed'
        );
    }
}
