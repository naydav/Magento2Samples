<?php
declare(strict_types=1);

namespace Engine\Test\Backend;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\DOMTestCase;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertFormFieldNotPresent
{
    /**
     * @param string $html
     * @param string $form
     * @param string $fieldset
     * @param string $field
     * @return void
     */
    public static function assert($html, $form, $fieldset, $field)
    {
        DOMTestCase::assertSelectCount('.form-inline', 1, $html, "Form container is missed");

        $pattern = '#<script type="text/x-magento-init">(.*' . $form . '.*)</script>#';
        if (!preg_match($pattern, $html, $matches)) {
            Assert::fail("Form '{$form}' initialization is missed");
        }
        Assert::assertJson($matches[1]);
        $config = json_decode($matches[1], true);

        if (!isset($config['*']['Magento_Ui/js/core/app']['components'][$form]['children'])) {
            Assert::fail("Form '{$form}' configuration is missed");
        }
        $formConfig = $config['*']['Magento_Ui/js/core/app']['components'][$form]['children'];

        if (!isset($formConfig[$form])) {
            Assert::fail("Form '{$form}' structure configuration is missed");
        }
        $formStructureConfig = $formConfig[$form];

        if (!isset($formStructureConfig['children'][$fieldset])) {
            Assert::fail("Fieldset '{$fieldset}' configuration is missed");
        }
        $fieldSetConfig = $formStructureConfig['children'][$fieldset];

        if (isset($fieldSetConfig['children'][$field])) {
            Assert::fail("Field '{$field}' configuration must not be present present");
        }
    }
}
