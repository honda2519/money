<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;

class MonthlyController extends Controller
{
    /** 月ごとの収支ページ。/months/2026-11 の形式。省略時は今月。 */
    public function show(?string $month = null): View
    {
        $current = $this->parseMonth($month);

        $transactions = Transaction::with('category')
            ->inMonth($current)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $income = $transactions->where('type', Transaction::INCOME)->sum('amount');
        $expense = $transactions->where('type', Transaction::EXPENSE)->sum('amount');

        // 支出のカテゴリ別内訳（多い順）
        $expenseByCategory = $transactions
            ->where('type', Transaction::EXPENSE)
            ->groupBy(fn (Transaction $t) => $t->category->name)
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc();

        return view('months.show', [
            'month' => $current,
            'prev' => $current->subMonth()->format('Y-m'),
            'next' => $current->addMonth()->format('Y-m'),
            'transactions' => $transactions,
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'expenseByCategory' => $expenseByCategory,
        ]);
    }

    private function parseMonth(?string $month): CarbonImmutable
    {
        if ($month !== null) {
            try {
                return CarbonImmutable::instance(Carbon::createFromFormat('!Y-m', $month))->startOfMonth();
            } catch (\Throwable) {
                // 不正な形式は今月にフォールバック
            }
        }

        return CarbonImmutable::now()->startOfMonth();
    }
}
