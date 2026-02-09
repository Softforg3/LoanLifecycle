<?php

namespace App\Services;

use App\Models\Loan;
use App\Exceptions\Domain\LoanCanNotBeApprovedException;
use App\Exceptions\Domain\PaymentCanNotBeAddedException;

class LoanService
{
    private InstallmentScheduleService $installmentScheduleService;

    public function __construct(InstallmentScheduleService $installmentScheduleService = null)
    {
        $this->installmentScheduleService = $installmentScheduleService ?? new InstallmentScheduleService();
    }

    public function createLoan(float $amount, int $termInMonths): Loan
    {
        return Loan::create([
            'amount' => $amount,
            'term' => $termInMonths,
            'status' => Loan::STATUS_CREATED,
        ]);
    }

    public function approveLoan(Loan $loan): Loan
    {
        $this->ensureLoanCanBeApproved($loan);

        $this->installmentScheduleService->createInstallmentsForLoan($loan);

        $loan->status = Loan::STATUS_OPEN;
        $loan->save();

        return $loan->load(['installments', 'payments']);
    }

    public function addPaymentToLoan(Loan $loan, float $paymentAmount): Loan
    {
        $this->ensurePaymentCanBeAdded($loan, $paymentAmount);

        $loan->payments()->create([
            'amount' => $paymentAmount,
        ]);

        $loan->refresh()->load(['installments', 'payments']);

        if ($loan->getOpenDebt() <= Loan::REPAYMENT_DEBT_TOLERANCE) {
            $loan->status = Loan::STATUS_REPAID;
            $loan->save();
        }

        return $loan;
    }

    private function ensureLoanCanBeApproved(Loan $loan): void
    {
        if ($loan->isOpen()) {
            throw LoanCanNotBeApprovedException::becauseLoanIsAlreadyOpen();
        }

        if ($loan->isRepaid()) {
            throw LoanCanNotBeApprovedException::becauseLoanIsAlreadyRepaid();
        }
    }

    private function ensurePaymentCanBeAdded(Loan $loan, float $paymentAmount): void
    {
        if (!$loan->isOpen()) {
            throw PaymentCanNotBeAddedException::becauseLoanIsNotOpen();
        }

        if ($paymentAmount <= 0) {
            throw PaymentCanNotBeAddedException::becausePaymentAmountIsInvalid();
        }

        $loan->loadMissing(['installments', 'payments']);

        if ($paymentAmount > $loan->getOpenDebt()) {
            throw PaymentCanNotBeAddedException::becausePaymentExceedsOpenDebt();
        }
    }
}
