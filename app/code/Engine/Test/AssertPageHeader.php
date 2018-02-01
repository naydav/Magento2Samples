<?php
declare(strict_types=1);

namespace Engine\Test;

use PHPUnit\Framework\DOMTestCase;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertPageHeader
{
    /**
     * @param string $html
     * @param string $header
     * @return void
     */
    public static function assert($html, $header)
    {
        DOMTestCase::assertSelectEquals('h1', $header, 1, $html, 'Page header is wrong');
    }
}
