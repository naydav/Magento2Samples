<?php
namespace Engine\Framework\Exception;

use Magento\Framework\Exception\ValidatorException as BaseValidatorException;
use Magento\Framework\Phrase;

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
     * @param Phrase $phrase
     * @param array $errors
     */
    public function __construct(Phrase $phrase, array $errors = [])
    {
        parent::__construct($phrase);
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
