<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function create(Request $request): JsonResponse
    {
        $data = $this->validate($request, [
            'amount' => ['required', 'numeric'],
            'term' => ['required', 'integer'],
        ]);

        $loan = $this->loanService->createLoan($data['amount'], $data['term']);

        return response()->json($loan, 201);
    }

    public function show(int $id): JsonResponse
    {
        $loan = Loan::with(['installments', 'payments'])->findOrFail($id);

        return response()->json($loan);
    }

    public function approve(int $id): JsonResponse
    {
        $loan = Loan::findOrFail($id);
        $loan = $this->loanService->approveLoan($loan);

        return response()->json($loan);
    }

    public function addPayment(Request $request, int $id): JsonResponse
    {
        $data = $this->validate($request, [
            'amount' => ['required', 'numeric'],
        ]);

        $loan = Loan::with(['installments', 'payments'])->findOrFail($id);
        $loan = $this->loanService->addPaymentToLoan($loan, $data['amount']);

        return response()->json($loan);
    }
}
