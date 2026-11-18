<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class TransactionController extends Controller
{
    public function create(Request $request): View
    {
        // ?month=2026-10 で来たら、その月の1日ではなく「今日 or その月末」を初期値にする
        $defaultDate = now()->toDateString();
        if (preg_match('/^\d{4}-\d{2}$/', (string) $request->query('month'))) {
            $m = Carbon::createFromFormat('!Y-m', $request->query('month'));
            if (! $m->isSameMonth(now())) {
                $defaultDate = $m->endOfMonth()->toDateString();
            }
        }

        return view('transactions.create', [
            'transaction' => new Transaction([
                'date' => $defaultDate,
                'type' => Transaction::EXPENSE,
            ]),
            'categories' => Category::orderBy('type', 'desc')->orderBy('id')->get(),
        ]);
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $transaction = Transaction::create($request->validated());

        return $this->backToMonth($transaction, '登録しました。');
    }

    public function edit(Transaction $transaction): View
    {
        return view('transactions.edit', [
            'transaction' => $transaction,
            'categories' => Category::orderBy('type', 'desc')->orderBy('id')->get(),
        ]);
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        return $this->backToMonth($transaction, '更新しました。');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return $this->backToMonth($transaction, '削除しました。');
    }

    private function backToMonth(Transaction $transaction, string $message): RedirectResponse
    {
        return redirect()
            ->route('months.show', ['month' => $transaction->date->format('Y-m')])
            ->with('status', $message);
    }
}
