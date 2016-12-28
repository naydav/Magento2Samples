<?php
namespace Engine\Backend\Test;

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
        \PHPUnit_Framework_Assert::assertSelectCount(
            '#page:main-container .admin__data-grid-outer-wrap',
            1,
            $html,
            'Grid is missed'
        );
    }
}
