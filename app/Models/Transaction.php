<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const INCOME = 'income';
    public const EXPENSE = 'expense';

    protected $fillable = ['date', 'type', 'category_id', 'amount', 'memo'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** 指定した月（その月の任意の日付でOK）の取引だけに絞る */
    public function scopeInMonth(Builder $query, CarbonInterface $month): Builder
    {
        return $query->whereBetween('date', [
            $month->copy()->startOfMonth()->toDateString(),
            $month->copy()->endOfMonth()->toDateString(),
        ]);
    }
}
