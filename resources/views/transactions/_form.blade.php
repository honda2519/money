@php
    $currentType = old('type', $transaction->type);
@endphp

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-5">
    {{-- 種別 --}}
    <div>
        <span class="block text-sm font-medium mb-1">種別</span>
        <div class="flex gap-4 text-sm">
            <label class="flex items-center gap-1">
                <input type="radio" name="type" value="expense" @checked($currentType === 'expense')> 支出
            </label>
            <label class="flex items-center gap-1">
                <input type="radio" name="type" value="income" @checked($currentType === 'income')> 収入
            </label>
        </div>
    </div>

    {{-- 日付 --}}
    <div>
        <label for="date" class="block text-sm font-medium mb-1">日付</label>
        <input id="date" type="date" name="date" required
               value="{{ old('date', optional($transaction->date)->toDateString()) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>

    {{-- カテゴリ --}}
    <div>
        <label for="category_id" class="block text-sm font-medium mb-1">カテゴリ</label>
        <select id="category_id" name="category_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">選択してください</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" data-type="{{ $category->type }}"
                    @selected((string) old('category_id', $transaction->category_id) === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- 金額 --}}
    <div>
        <label for="amount" class="block text-sm font-medium mb-1">金額（円）</label>
        <input id="amount" type="number" name="amount" min="1" step="1" required inputmode="numeric"
               value="{{ old('amount', $transaction->amount) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>

    {{-- メモ --}}
    <div>
        <label for="memo" class="block text-sm font-medium mb-1">メモ（任意）</label>
        <input id="memo" type="text" name="memo" maxlength="255"
               value="{{ old('memo', $transaction->memo) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-700">保存</button>
        <a href="{{ url()->previous() }}" class="text-sm text-slate-500 hover:underline">キャンセル</a>
    </div>
</div>

<script>
    // 種別（支出/収入）に合ったカテゴリだけを選べるようにする
    (function () {
        const select = document.getElementById('category_id');
        const radios = document.querySelectorAll('input[name="type"]');

        function sync() {
            const type = document.querySelector('input[name="type"]:checked').value;
            Array.from(select.options).forEach(function (o) {
                if (!o.value) return;
                const match = o.dataset.type === type;
                o.hidden = !match;
                o.disabled = !match;
            });
            const selected = select.selectedOptions[0];
            if (selected && selected.value && selected.disabled) select.value = '';
        }

        radios.forEach(function (r) { r.addEventListener('change', sync); });
        sync();
    })();
</script>
