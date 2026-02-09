<?php

namespace Tests;

use App\Models\Loan;
use App\Services\LoanService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LoanRepaymentTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    private LoanService $loanService;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        $this->loanService = new LoanService();
    }

    public function testAddPaymentToLoan(): void
    {
        $loan = $this->setUpLoanWithStatus(2);
        $loan = $this->setUpInstallmentsForLoan($loan);

        $loanWithPayment = $this->loanService->addPaymentToLoan($loan, 171.56);

        self::assertEquals(2, $loanWithPayment->status);
        self::assertEqualsWithDelta(857.8, $loanWithPayment->open_debt, 0.011);
    }

    public function testRepayLoan(): void
    {
        $loan = $this->setUpLoanWithStatus(2);
        $loan = $this->setUpInstallmentsForLoan($loan);

        foreach ($loan->installments as $installment) {
            $loan = $this->loanService->addPaymentToLoan($loan, $installment->principal + $installment->interest);
        }

        self::assertEquals(3, $loan->status);
        self::assertEqualsWithDelta(0, $loan->open_debt, 0.011);
    }

    public function testCannotAddPaymentToCreatedLoan(): void
    {
        $loan = $this->setUpLoanWithStatus(1);

        $this->expectException(\Exception::class);

        $this->loanService->addPaymentToLoan($loan, 171.56);
    }

    public function testCannotAddPaymentToRepaidLoan(): void
    {
        $loan = $this->setUpLoanWithStatus(3);

        $this->expectException(\Exception::class);

        $this->loanService->addPaymentToLoan($loan, 171.56);
    }

    public function testCannotAddNegativePayment(): void
    {
        $loan = $this->setUpLoanWithStatus(2);

        $this->expectException(\Exception::class);

        $this->loanService->addPaymentToLoan($loan, -1);
    }


    public function testCannotOverpay(): void
    {
        $loan = $this->setUpLoanWithStatus(2);
        $loan = $this->setUpInstallmentsForLoan($loan);

        $this->expectException(\Exception::class);

        $this->loanService->addPaymentToLoan($loan, 1039.37);
    }

    private function setUpLoanWithStatus(int $status): Loan
    {
        return factory(Loan::class)->create([
            'amount' => 1000,
            'term' => 6,
            'status' => $status,
        ]);
    }

    private function setUpInstallmentsForLoan(Loan $loan): Loan
    {
        $now = \Carbon\Carbon::now();

        $loan->installments()->createMany([
            [
                'principal' => 163.23,
                'interest' => 8.33,
                'due_date' => $now->addMonths(1),
            ], [
                'principal' => 164.59,
                'interest' => 6.97,
                'due_date' => $now->addMonths(2),
            ], [
                'principal' => 165.96,
                'interest' => 5.60,
                'due_date' => $now->addMonths(3),
            ], [
                'principal' => 167.34,
                'interest' => 4.22,
                'due_date' => $now->addMonths(4),
            ], [
                'principal' => 168.74,
                'interest' => 2.82,
                'due_date' => $now->addMonths(5),
            ], [
                'principal' => 170.14,
                'interest' => 1.42,
                'due_date' => $now->addMonths(6),
            ]
        ]);

        return $loan;
    }
}
