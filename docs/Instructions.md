# Attention: Please refrain from using AI, pasting larger amounts of code or working outside of the online editor!

We know that it is inconvenient to work inside this online editor but it enables proper scoring of your valued effort.

# Context

Be aware that the assessment only allows PHP version 7.4.

This assessment is designed to give you a decent challenge, don't be discouraged if 100% completion is not achievable in time.

Your challenge today is to implement an Installment Loan Lifecycle.
To accomplish this you are going to use some parts of the [Lumen Framework](https://lumen.laravel.com/docs/7.x).

The Lifecycle of a Loan for this Assessment is as follows:

1. A Loan is "Created"
2. A Loan gets approved, its Installments get scheduled and its Status is set to "Open"
3. A Loan is "Repaid", when there are enough payments to cover all the cost of the Loan's Installments.

# Objectives

## LoanService

The main objective of this challenge is to implement the code that can create and update a Loan. The entrypoint for this set of features is the LoanService class. It should handle following use cases:

### 1. Create a Loan

To create a Loan you need to define its term in months and its amount.
Every Loan should get created with the status "Created" and no Installments.
This functionality is already implemented and serves as a little orientation for the other parts of implementation.

### 2. Approve a stored Loan

Only Loans in Status "Created" can be approved. If it is called with a Loan in Status "Open" or "Repaid", this should return with an error.

This function should return the Loan updated to Status "Open" and with its Installments.

### 3. Add payments

Only Loans that are in Status "Open" can get a new Payment assigned. Payments need to have an amount greater than 0 and should only be applied if they don't cover more than the open debt of the loan.

Add payments to the Loan and return the Loan with its remaining open debt.

## Http Endpoints

When you are confident that your business logic works as it should, you should as well add some http endpoints.
Those endpoints should offer access to the 3 use cases you implemented, as well as just showing a stored Loan with its Installments and Payments. Routes are connected through controllers in the `routes/web.php` file. For more info see the [Lumen Docs about Controllers](https://lumen.laravel.com/docs/7.x/controllers#basic-controllers).

As an example, the following code shows the function you need to place inside a controller and connect to a route to create a loan via http endpoint:

```php
public function functionName(Request $request, LoanService $loanService)
{
    $data = $this->validate($request, [
        'amount' => ['required', 'numeric'],
        'term' => ['required', 'integer'],
    ]);

    return $loanService->createLoan($data['amount'], $data['term']);
}
```

# Guidance

## General Advice

- It is recommended to start with the the business logic. Don't focus on the results of HttpTest until you have the business logic properly in place.
- Most complex part is the installments calculation.
- You are free to structure the business logic as you see fit (e.g. introduce more classes)
- You can add additional tests as you see fit, but don't forget that the challenge will be scored against the predefined tests.
- If you want to add more fields to the models, feel free to do so. Don't forget to add/update the migration files and factories in that case.
- Take a look at the provided `routes/web.php` file and the phpunit tests to check which endpoints you need to provide and how their api (input/output) looks like.

## How an Installment Loan works

Installment Loans are a form of Loan where you lend a principal amount from the creditor and then repay that debt in equal monthly payments. The interest for each installment is calculated based on the remaining total principal.

Here's a simple schedule of an Installment Loan with `principal = 1000`, `number of payments = 6` and `annual interest rate = 10%` to illustrate this concept:

| Nr | Principal | Interest | Total  |
|----|-----------|----------|--------|
| 1  | 163.23    | 8.33     | 171.56 |
| 2  | 164.59    | 6.97     | 171.56 |
| 3  | 165.96    | 5.60     | 171.56 |
| 4  | 167.34    | 4.22     | 171.56 |
| 5  | 168.74    | 2.82     | 171.56 |
| 6  | 170.14    | 1.42     | 171.56 |

### Formulas that help you get the job done:

```
Monthly Interest Rate = Annual Interest Rate / 12
```

```
Monthly Installment Amount = [P × (r × (1 + r)^n)] / [(1 + r)^n - 1]
```

where:
- P represents the loan's principal
- r is the monthly interest rate
- n is the total number of monthly payments
