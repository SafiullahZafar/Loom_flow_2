<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\FixedExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedExpense extends Model
{
    /** @use HasFactory<FixedExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'payee',
        'default_amount',
        'frequency',
        'due_day',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'due_day' => 'integer',
        'is_active' => 'boolean',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * The date of the most recent recorded payment, or null if never paid.
     * Uses already-loaded relation when available to avoid N+1.
     */
    public function lastPaidDate(): ?Carbon
    {
        if ($this->relationLoaded('expenses')) {
            $latest = $this->expenses->first()?->expense_date;

            return $latest ? Carbon::parse($latest) : null;
        }

        $latest = $this->expenses()
            ->latest('expense_date')
            ->latest('id')
            ->value('expense_date');

        return $latest ? Carbon::parse($latest) : null;
    }

    /**
     * Start of the current billing period based on frequency.
     * Weekly  → start of this week (Monday)
     * Monthly → start of this month
     * Quarterly → start of this quarter
     * Yearly  → start of this year
     */
    public function currentPeriodStart(): Carbon
    {
        return match ($this->frequency) {
            'weekly' => now()->startOfWeek(),
            'quarterly' => now()->startOfQuarter(),
            'yearly' => now()->startOfYear(),
            default => now()->startOfMonth(), // monthly
        };
    }

    /**
     * End of the current billing period.
     */
    public function currentPeriodEnd(): Carbon
    {
        return match ($this->frequency) {
            'weekly' => now()->endOfWeek(),
            'quarterly' => now()->endOfQuarter(),
            'yearly' => now()->endOfYear(),
            default => now()->endOfMonth(),
        };
    }

    /**
     * Whether a payment has already been recorded in the current billing period.
     * Uses already-loaded relation when available to avoid N+1.
     */
    public function isPaidForCurrentPeriod(): bool
    {
        $start = $this->currentPeriodStart();
        $end = $this->currentPeriodEnd();

        if ($this->relationLoaded('expenses')) {
            return $this->expenses->contains(
                fn (Expense $e) => Carbon::parse($e->expense_date)->between($start, $end)
            );
        }

        return $this->expenses()
            ->whereBetween('expense_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->exists();
    }

    /**
     * The next due date — i.e. the start of the next billing period.
     */
    public function nextDueDate(): Carbon
    {
        $next = match ($this->frequency) {
            'weekly' => now()->startOfWeek()->addWeek(),
            'quarterly' => now()->startOfQuarter()->addQuarter(),
            'yearly' => now()->startOfYear()->addYear(),
            default => now()->startOfMonth()->addMonth(),
        };

        // If a specific due_day is set for monthly, use it
        if ($this->frequency === 'monthly' && $this->due_day) {
            $next->day(min((int) $this->due_day, $next->daysInMonth));
        }

        return $next;
    }

    /**
     * Human-readable label for the current period.
     * e.g. "Week of 13 Oct", "October 2026", "Q4 2026", "2026"
     */
    public function currentPeriodLabel(): string
    {
        return match ($this->frequency) {
            'weekly' => 'Week of '.$this->currentPeriodStart()->format('d M'),
            'quarterly' => 'Q'.$this->currentPeriodStart()->quarter.' '.$this->currentPeriodStart()->year,
            'yearly' => (string) now()->year,
            default => now()->format('F Y'),
        };
    }
}
