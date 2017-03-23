<?php
namespace Engine\Test;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertPageTitle
{
    /**
     * @param string $html
     * @param string $title
     * @param string $regexSeparator
     * @return void
     */
    public static function assert($html, $title, $regexSeparator = '~')
    {
        $title = str_replace(['(', ')'], ['\(', '\)'], $title);
        \PHPUnit_Framework_Assert::assertSelectRegExp(
            'title',
            "{$regexSeparator}{$title}.*{$regexSeparator}",
            1,
            $html,
            'Meta title is wrong'
        );
    }
}
