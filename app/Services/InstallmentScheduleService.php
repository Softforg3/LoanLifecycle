<?php

namespace App\Services;

use App\Models\Loan;
use DateTime;

class InstallmentScheduleService
{
    private const ANNUAL_INTEREST_RATE = 0.10;
    private const MONTHS_IN_YEAR = 12;
    private const DECIMAL_PRECISION = 2;

    public function generateSchedule(float $loanAmount, int $termInMonths): array
    {
        $monthlyInterestRate = $this->calculateMonthlyInterestRate();
        $monthlyPayment = $this->calculateMonthlyPayment(
            $loanAmount,
            $monthlyInterestRate,
            $termInMonths
        );

        $remainingPrincipal = $loanAmount;
        $installments = [];

        for ($monthNumber = 1; $monthNumber <= $termInMonths; $monthNumber++) {
            $installment = $this->createInstallment(
                $monthNumber,
                $remainingPrincipal,
                $monthlyInterestRate,
                $monthlyPayment
            );

            $installments[] = $installment;
            $remainingPrincipal -= $installment['principal'];
        }

        return $installments;
    }

    private function createInstallment(
        int $monthNumber,
        float $remainingPrincipal,
        float $monthlyInterestRate,
        float $monthlyPayment
    ): array {
        $interest = $remainingPrincipal * $monthlyInterestRate;
        $principal = $monthlyPayment - $interest;

        return [
            'principal' => round($principal, self::DECIMAL_PRECISION),
            'interest' => round($interest, self::DECIMAL_PRECISION),
            'due_date' => $this->calculateDueDate($monthNumber),
        ];
    }

    public function createInstallmentsForLoan(Loan $loan): void
    {
        $installments = $this->generateSchedule($loan->amount, $loan->term);
        $loan->installments()->createMany($installments);
    }

    private function calculateMonthlyInterestRate(): float
    {
        return self::ANNUAL_INTEREST_RATE / self::MONTHS_IN_YEAR;
    }

    private function calculateMonthlyPayment(
        float $loanAmount,
        float $monthlyInterestRate,
        int $termInMonths
    ): float {
        $denominator = pow(1 + $monthlyInterestRate, $termInMonths);

        return ($loanAmount * ($monthlyInterestRate * $denominator)) / ($denominator - 1);
    }

    private function calculateDueDate(int $monthsFromNow): DateTime
    {
        $dueDate = new DateTime();
        $dueDate->modify("+{$monthsFromNow} months");

        return $dueDate;
    }
}
