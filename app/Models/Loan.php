<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{

    const STATUS_CREATED = 1;
    const STATUS_OPEN = 2;
    const STATUS_REPAID = 3;

    const STATUS_NAMES = [
        self::STATUS_CREATED => 'Created',
        self::STATUS_OPEN => 'Open',
        self::STATUS_REPAID => 'Repaid',
    ];

    const REPAYMENT_DEBT_TOLERANCE = 0.01;

    protected $fillable = [
        'amount',
        'term',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
        'term' => 'integer',
        'amount' => 'decimal:2',
    ];

    protected $appends = ['open_debt'];

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getOpenDebtAttribute(): float
    {
        return round($this->installments->sum('principal') + $this->installments->sum('interest') - $this->payments->sum('amount'), 2);
    }

    public function isCreated(): bool
    {
        return $this->status === self::STATUS_CREATED;
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isRepaid(): bool
    {
        return $this->status === self::STATUS_REPAID;
    }

    public function getOpenDebt(): float
    {
        return $this->open_debt;
    }

}
