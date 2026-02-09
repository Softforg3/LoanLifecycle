<?php

namespace Tests;

use App\Models\Loan;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HttpTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * this function passes from the get go, as the tested
     * controller is provided as an example
     */
    public function testExampleFunction(): void
    {
        $this->post('/example', ['post_field_name' => 100]);

        $response = json_decode($this->response->getContent());

        self::assertEquals(100, $response->exampleOutput);
    }

    public function testCreateLoan(): void
    {
        $this->post('/api/loans', ['term' => 6, 'amount' => 1000]);

        $loan = json_decode($this->response->getContent());

        self::assertNotEmpty($loan->id);
        self::assertEquals(6, $loan->term);
        self::assertEquals(1, $loan->status);
        self::assertEquals(1000, $loan->amount);
        self::assertEquals(0, $loan->open_debt);
        self::assertNotEmpty($loan->created_at);
        self::assertNotEmpty($loan->updated_at);
    }

    public function testShowLoan(): void
    {
        $loan = factory(Loan::class)->create();

        $this->get(sprintf('/api/loans/%s', $loan->id));

        $loanFromResponse = json_decode($this->response->getContent());

        self::assertEquals($loan->id, $loanFromResponse->id);
        self::assertEquals($loan->status, $loanFromResponse->status);
        self::assertEquals($loan->amount, $loanFromResponse->amount);
        self::assertEquals($loan->term, $loanFromResponse->term);
        self::assertIsArray($loanFromResponse->installments);
        self::assertIsArray($loanFromResponse->payments);
    }

    public function testApproveLoan(): void
    {
        $loan = factory(Loan::class)->create([
            'amount' => 1000,
            'term' => 6,
            'status' => 1,
        ]);
        $this->post(
            sprintf('/api/loans/%s/approve', $loan->id)
        );

        $installments = json_decode($this->response->getContent())->installments;

        $sum = 0;
        foreach ($installments as $installment) {
            $sum += $installment->principal + $installment->interest;
        }

        self::assertEqualsWithDelta(1029.37, $sum, 0.011);
    }

    public function testErrorCodeWhenApprovalDoesNotWork(): void
    {
        $loan = factory(Loan::class)->create([
            'amount' => 1000,
            'term' => 6,
            'status' => 2,
        ]);

        $response = $this->post(
            sprintf('/api/loans/%s/approve', $loan->id)
        );

        $response->seeStatusCode(512);
    }

    public function testAddPaymentToLoan(): void
    {
        $loan = $this->setUpLoanWithStatus(2);
        $loan = $this->setUpInstallmentsForLoan($loan);

        $this->post(
            sprintf('/api/loans/%s/addPayment', $loan->id),
            ['amount' => 171.56]
        );

        $loan = json_decode($this->response->getContent());

        self::assertEquals(2, $loan->status);
        self::assertEqualsWithDelta(857.8, $loan->open_debt, 0.011);
    }

    public function testErrorCodeWhenAddingPaymentDoesNotWork(): void
    {
        $loan = $this->setUpLoanWithStatus(3);

        $this->post(
            sprintf('/api/loans/%s/addPayment', $loan->id),
            ['amount' => 171.56]
        );

        $this->seeStatusCode(512);
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
