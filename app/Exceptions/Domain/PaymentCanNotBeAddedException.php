<?php

namespace App\Exceptions\Domain;

use DomainException;

class PaymentCanNotBeAddedException extends DomainException
{
    public static function becauseLoanIsNotOpen(): self
    {
        return new self('Payment cannot be added because the loan is not open.');
    }

    public static function becausePaymentAmountIsInvalid(): self
    {
        return new self('Payment cannot be added because the amount is invalid. Amount must be greater than zero.');
    }

    public static function becausePaymentExceedsOpenDebt(): self
    {
        return new self('Payment cannot be added because the payment amount exceeds the open debt.');
    }
}
