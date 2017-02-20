/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

var config = {
    map: {
        '*': {
            EngineDependOnOptionFieldset: 'Engine_Backend/js/form/depend-on-option/fieldset',
            EngineDependOnOptionInput: 'Engine_Backend/js/form/depend-on-option/input',
            EngineDependOnOptionSelect: 'Engine_Backend/js/form/depend-on-option/select',
            EngineDependOnOptionTextarea: 'Engine_Backend/js/form/depend-on-option/textarea',
            EngineDependOnOptionYesNo: 'Engine_Backend/js/form/depend-on-option/yesno'
        }
    },
    config: {
        mixins: {
            'mage/validation': {
                'Engine_Backend/js/form/validation/validate-greater-than-field-value': true
            }
        }
    }
};
