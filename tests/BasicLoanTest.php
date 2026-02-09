<?php

namespace Tests;

use App\Models\Loan;
use App\Services\LoanService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BasicLoanTest extends TestCase
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

    public function testCreateLoan(): void
    {
        $loan = $this->loanService->createLoan(1000.0, 6);

        self::assertInstanceOf(Loan::class, $loan);
        self::assertNotEmpty($loan->id);
        self::assertEquals(6, $loan->term);
        self::assertEquals(1, $loan->status);
        self::assertEquals(1000, $loan->amount);
        self::assertEquals(0, $loan->open_debt);
        self::assertNotEmpty($loan->created_at);
        self::assertNotEmpty($loan->updated_at);
    }
}
