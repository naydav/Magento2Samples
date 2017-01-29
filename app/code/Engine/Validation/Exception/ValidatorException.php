<?php
namespace Engine\Validation\Exception;

use Magento\Framework\Exception\ValidatorException as BaseValidatorException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidatorException extends BaseValidatorException
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @param array $errors
     * @param \Exception $previous
     */
    public function __construct(array $errors = [], \Exception $previous = null)
    {
        $errorsCount = count($errors);
        if ($errorsCount) {
            $message = $errorsCount == 1 ? reset($errors) : __(implode('; ', $errors));
        } else {
            $message = __('Entity isn\'t valid.');
        }
        parent::__construct($message, $previous);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
