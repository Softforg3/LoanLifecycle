<?php

namespace App\Exceptions\Domain;

use DomainException;

class LoanCanNotBeApprovedException extends DomainException
{
    public static function becauseLoanIsAlreadyOpen(): self
    {
        return new self('Loan cannot be approved because it is already open.');
    }

    public static function becauseLoanIsAlreadyRepaid(): self
    {
        return new self('Loan cannot be approved because it is already repaid.');
    }
}
