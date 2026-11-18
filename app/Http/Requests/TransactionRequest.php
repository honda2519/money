<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認証を導入したらここで本人確認を行う
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'type' => ['required', Rule::in([Transaction::INCOME, Transaction::EXPENSE])],
            // 選んだ収入/支出の種類と一致するカテゴリだけ許可する
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('type', $this->input('type')),
            ],
            'amount' => ['required', 'integer', 'min:1', 'max:99999999'],
            'memo' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => '日付',
            'type' => '種別',
            'category_id' => 'カテゴリ',
            'amount' => '金額',
            'memo' => 'メモ',
        ];
    }
}
