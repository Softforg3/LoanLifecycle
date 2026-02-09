# UWAGA: Prosimy o nieprzestrzeganie AI, wklejania większych ilości kodu lub pracy poza edytorem online!

Wiemy, że praca w tym edytorze online jest niewygodna, ale umożliwia prawidłową ocenę Twojego cennego wysiłku.

# Kontekst

Należy pamiętać, że assessment pozwala tylko na PHP w wersji 7.4.

Ten assessment jest zaprojektowany tak, aby dać Ci przyzwoite wyzwanie, nie zniechęcaj się, jeśli 100% ukończenia nie jest osiągalne w czasie.

Twoim dzisiejszym wyzwaniem jest zaimplementowanie cyklu życia pożyczki ratalnej (Installment Loan Lifecycle).
Aby to osiągnąć, użyjesz niektórych części [Lumen Framework](https://lumen.laravel.com/docs/7.x).

Cykl życia pożyczki (Loan) dla tego Assessment wygląda następująco:

1. Pożyczka zostaje "Utworzona" (Created)
2. Pożyczka zostaje zatwierdzona, jej raty zostają zaplanowane, a jej status ustawiony na "Otwarta" (Open)
3. Pożyczka jest "Spłacona" (Repaid), gdy jest wystarczająco płatności, aby pokryć wszystkie koszty rat pożyczki.

# Cele

## LoanService

Głównym celem tego wyzwania jest zaimplementowanie kodu, który może tworzyć i aktualizować pożyczkę (Loan). Punkt wejścia dla tego zestawu funkcji to klasa LoanService. Powinna obsługiwać następujące przypadki użycia:

### 1. Utwórz pożyczkę (Create a Loan)

Aby utworzyć pożyczkę, musisz zdefiniować jej termin w miesiącach i kwotę.
Każda pożyczka powinna zostać utworzona ze statusem "Created" i bez rat (Installments).
Ta funkcjonalność jest już zaimplementowana i służy jako mała orientacja dla innych części implementacji.

### 2. Zatwierdź zapisaną pożyczkę (Approve a stored Loan)

Tylko pożyczki w statusie "Created" mogą być zatwierdzone. Jeśli zostanie wywołana z pożyczką w statusie "Open" lub "Repaid", powinno to zwrócić błąd.

Ta funkcja powinna zwrócić pożyczkę zaktualizowaną do statusu "Open" i z jej ratami (Installments).

### 3. Dodaj płatności (Add payments)

Tylko pożyczki w statusie "Open" mogą otrzymać nową płatność (Payment). Płatności muszą mieć kwotę większą niż 0 i powinny być stosowane tylko wtedy, gdy nie pokrywają więcej niż otwarte zadłużenie pożyczki.

Dodaj płatności do pożyczki i zwróć pożyczkę z jej pozostałym otwartym zadłużeniem.

## Endpointy HTTP

Kiedy będziesz pewien, że Twoja logika biznesowa działa tak, jak powinna, powinieneś również dodać kilka endpointów HTTP.
Te endpointy powinny oferować dostęp do 3 przypadków użycia, które zaimplementowałeś, a także po prostu pokazywać zapisaną pożyczkę z jej ratami i płatnościami. Trasy są połączone przez kontrolery w pliku `routes/web.php`. Więcej informacji w [dokumentacji Lumen o kontrolerach](https://lumen.laravel.com/docs/7.x/controllers#basic-controllers).

Jako przykład, następujący kod pokazuje funkcję, którą musisz umieścić w kontrolerze i połączyć z trasą, aby utworzyć pożyczkę przez endpoint HTTP:

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

# Wskazówki

## Ogólne porady

- Zaleca się rozpoczęcie od logiki biznesowej. Nie skupiaj się na wynikach HttpTest, dopóki nie będziesz mieć logiki biznesowej prawidłowo na miejscu.
- Najbardziej złożoną częścią jest obliczanie rat (installments calculation).
- Możesz swobodnie strukturyzować logikę biznesową tak, jak uważasz za stosowne (np. wprowadzić więcej klas).
- Możesz dodać dodatkowe testy, jak uważasz za stosowne, ale nie zapominaj, że wyzwanie będzie oceniane względem predefiniowanych testów.
- Jeśli chcesz dodać więcej pól do modeli, nie krępuj się. Nie zapomnij w takim przypadku dodać/zaktualizować pliki migracji i factories.
- Przyjrzyj się dostarczonemu plikowi `routes/web.php` oraz testom phpunit, aby sprawdzić, które endpointy musisz dostarczyć i jak wygląda ich API (input/output).

## Jak działa pożyczka ratalna (Installment Loan)

Pożyczki ratalne to forma pożyczki, w której pożyczasz kwotę główną (principal amount) od wierzyciela, a następnie spłacasz ten dług w równych miesięcznych płatnościach. Odsetki dla każdej raty są obliczane na podstawie pozostałej całkowitej kwoty głównej.

Oto prosty harmonogram pożyczki ratalnej z `kwotą główną = 1000`, `liczba płatności = 6` i `roczną stopą oprocentowania = 10%`, aby zilustrować tę koncepcję:

| Nr | Kwota główna | Odsetki | Razem  |
|----|--------------|---------|--------|
| 1  | 163.23       | 8.33    | 171.56 |
| 2  | 164.59       | 6.97    | 171.56 |
| 3  | 165.96       | 5.60    | 171.56 |
| 4  | 167.34       | 4.22    | 171.56 |
| 5  | 168.74       | 2.82    | 171.56 |
| 6  | 170.14       | 1.42    | 171.56 |

### Formuły, które pomogą Ci wykonać zadanie:

```
Miesięczna stopa oprocentowania = Roczna stopa oprocentowania / 12
```

```
Miesięczna kwota raty = [P × (r × (1 + r)^n)] / [(1 + r)^n - 1]
```

gdzie:
- P reprezentuje kwotę główną pożyczki (principal)
- r to miesięczna stopa oprocentowania
- n to całkowita liczba miesięcznych płatności

---

## Dodatkowe informacje:

**Status pożyczki (Loan Status):**
- **Created** - Utworzona
- **Open** - Otwarta
- **Repaid** - Spłacona

**Kluczowe pojęcia:**
- **Loan** - Pożyczka
- **Installment** - Rata
- **Payment** - Płatność
- **Principal** - Kwota główna
- **Interest** - Odsetki
- **Term** - Okres (w miesiącach)
- **Annual Interest Rate** - Roczna stopa oprocentowania (10% w zadaniu)
- **Monthly Interest Rate** - Miesięczna stopa oprocentowania (Annual / 12)
