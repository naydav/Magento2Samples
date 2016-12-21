<?php
namespace Engine\Backend\Test;

/**
 * "Ad hock" for simple check
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssertFormFieldset
{
    /**
     * @param string $content
     * @param string $form
     * @param string $fieldset
     * @return void
     */
    public static function assert($content, $form, $fieldset)
    {
        \PHPUnit_Framework_Assert::assertSelectCount('.form-inline', 1, $content, "Form container is missed");

        $pattern = '#<script type="text/x-magento-init">(.*' . $form . '.*)</script>#';
        if (!preg_match($pattern, $content, $matches)) {
            \PHPUnit_Framework_Assert::fail("Form '{$form}' initialization is missed");
        }
        $config = json_decode($matches[1], true);

        if (!isset($config['*']['Magento_Ui/js/core/app']['components'][$form]['children'])) {
            \PHPUnit_Framework_Assert::fail("Form '{$form}' configuration is missed");
        }
        $formConfig = $config['*']['Magento_Ui/js/core/app']['components'][$form]['children'];

        if (!isset($formConfig[$form])) {
            \PHPUnit_Framework_Assert::fail("Form '{$form}' structure configuration is missed");
        }
        $formStructureConfig = $formConfig[$form];

        if (!isset($formStructureConfig['children'][$fieldset])) {
            \PHPUnit_Framework_Assert::fail("Fieldset '{$fieldset}' configuration is missed");
        }
    }
}
