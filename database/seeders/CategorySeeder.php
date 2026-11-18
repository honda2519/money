<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $income = ['給与', '賞与', '副収入', 'その他収入'];
        $expense = [
            '食費', '日用品', '交通費', '住居費', '水道光熱費', '通信費',
            '医療費', '趣味・娯楽', '交際費', '衣服・美容', '教育', 'その他支出',
        ];

        foreach ($income as $name) {
            Category::firstOrCreate(['name' => $name, 'type' => 'income']);
        }
        foreach ($expense as $name) {
            Category::firstOrCreate(['name' => $name, 'type' => 'expense']);
        }
    }
}
