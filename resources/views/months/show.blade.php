@extends('layouts.app')

@section('title', $month->format('Y年n月') . 'の収支')

@section('content')
    {{-- 月の切り替え --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('months.show', $prev) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">← 前月</a>

        <div class="flex items-center gap-2">
            <h1 class="text-xl font-bold">{{ $month->format('Y年n月') }}</h1>
            <input type="month" value="{{ $month->format('Y-m') }}"
                   class="rounded-lg border border-slate-300 bg-white px-2 py-1 text-sm"
                   onchange="if (this.value) location.href = '{{ url('/months') }}/' + this.value">
        </div>

        <a href="{{ route('months.show', $next) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">翌月 →</a>
    </div>

    {{-- サマリー --}}
    <div class="grid grid-cols-3 gap-3 mb-8">
        <div class="rounded-xl bg-white p-4 border border-slate-200">
            <div class="text-xs text-slate-500">収入</div>
            <div class="mt-1 text-lg font-bold text-emerald-600">¥{{ number_format($income) }}</div>
        </div>
        <div class="rounded-xl bg-white p-4 border border-slate-200">
            <div class="text-xs text-slate-500">支出</div>
            <div class="mt-1 text-lg font-bold text-rose-600">¥{{ number_format($expense) }}</div>
        </div>
        <div class="rounded-xl bg-white p-4 border border-slate-200">
            <div class="text-xs text-slate-500">収支</div>
            <div class="mt-1 text-lg font-bold {{ $balance < 0 ? 'text-rose-600' : 'text-slate-800' }}">
                {{ $balance < 0 ? '-' : '' }}¥{{ number_format(abs($balance)) }}
            </div>
        </div>
    </div>

    {{-- 支出のカテゴリ別内訳 --}}
    @if ($expenseByCategory->isNotEmpty())
        <section class="mb-8">
            <h2 class="mb-3 text-sm font-semibold text-slate-600">支出の内訳</h2>
            <div class="rounded-xl bg-white p-4 border border-slate-200 space-y-3">
                @foreach ($expenseByCategory as $name => $total)
                    @php $percent = $expense > 0 ? round($total / $expense * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm">
                            <span>{{ $name }}</span>
                            <span class="text-slate-600">¥{{ number_format($total) }}（{{ $percent }}%）</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-rose-400" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 取引一覧 --}}
    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-600">明細（{{ $transactions->count() }}件）</h2>
            <a href="{{ route('transactions.create', ['month' => $month->format('Y-m')]) }}"
               class="text-sm text-emerald-700 hover:underline">この月に追加</a>
        </div>

        @if ($transactions->isEmpty())
            <div class="rounded-xl bg-white p-8 text-center text-sm text-slate-500 border border-slate-200">
                この月の記録はまだありません。
            </div>
        @else
            <div class="overflow-hidden rounded-xl bg-white border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs text-slate-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">日付</th>
                            <th class="px-4 py-2 font-medium">カテゴリ</th>
                            <th class="px-4 py-2 font-medium">メモ</th>
                            <th class="px-4 py-2 text-right font-medium">金額</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transactions as $t)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $t->date->format('n/j') }}</td>
                                <td class="px-4 py-2">{{ $t->category->name }}</td>
                                <td class="px-4 py-2 text-slate-500">{{ $t->memo }}</td>
                                <td class="px-4 py-2 text-right whitespace-nowrap font-medium {{ $t->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $t->type === 'income' ? '+' : '-' }}¥{{ number_format($t->amount) }}
                                </td>
                                <td class="px-4 py-2 text-right whitespace-nowrap">
                                    <a href="{{ route('transactions.edit', $t) }}" class="text-slate-500 hover:underline">編集</a>
                                    <form method="POST" action="{{ route('transactions.destroy', $t) }}" class="inline"
                                          onsubmit="return confirm('削除しますか？')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ml-2 text-rose-500 hover:underline">削除</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
