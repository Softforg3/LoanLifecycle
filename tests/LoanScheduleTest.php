<?php

namespace Tests;

use App\Models\Loan;
use App\Services\LoanService;
use Exception;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LoanScheduleTest extends TestCase
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

    public function testApproveLoan(): void
    {
        $loan = factory(Loan::class)->create([
            'amount' => 1000,
            'term' => 6,
            'status' => 1,
        ]);

        $approvedLoan = $this->loanService->approveLoan($loan);

        self::assertSame(2, $approvedLoan->status);

        $sum = 0;
        foreach ($approvedLoan->installments as $installment) {
            $sum += $installment->principal + $installment->interest;
        }

        self::assertEqualsWithDelta(1029.37, $sum, 0.011);
    }

    public function testAlreadyOpenLoanCannotBeApproved(): void
    {
        $loan = factory(Loan::class)->create([
            'amount' => 1000,
            'term' => 6,
            'status' => 2,
        ]);

        $this->expectException(Exception::class);

        $this->loanService->approveLoan($loan);
    }

    public function testAlreadyRepaidLoanCannotBeApproved(): void
    {
        $loan = factory(Loan::class)->create([
            'amount' => 1000,
            'term' => 6,
            'status' => 3,
        ]);

        $this->expectException(Exception::class);

        $this->loanService->approveLoan($loan);
    }

    public function testApproveExpensiveLongRunningLoan(): void
    {
        $loan = factory(Loan::class)->create([
            'amount' => 10000,
            'term' => 60,
            'status' => 1,
        ]);

        $approvedLoan = $this->loanService->approveLoan($loan);

        $sum = 0;
        foreach ($approvedLoan->installments as $installment) {
            $sum += $installment->principal + $installment->interest;
        }

        self::assertEqualsWithDelta(12748.23, $approvedLoan->open_debt, 0.011);
        self::assertEqualsWithDelta(12748.23, $sum, 0.011);
    }
}
