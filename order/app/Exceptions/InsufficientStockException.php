<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    /**
     * @param $productId
     * @param $message
     * @param $code
     * @param Exception|null $previous
     */
    public function __construct($productId, $message = "Not enough stock for product", $code = 400, Exception $previous = null)
    {
        $message = "$message: $productId";
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
