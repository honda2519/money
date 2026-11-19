<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * 公開デモ用の架空データ。直近3か月分（今月・先月・先々月）を作る。
 * 実行のたびに「今日」を基準に作るので、いつ開いても最近の月にデータがある。
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        $today = CarbonImmutable::today();

        for ($i = 2; $i >= 0; $i--) {
            $month = $today->startOfMonth()->subMonths($i);
            $this->seedMonth($month, $today);
        }
    }

    private function seedMonth(CarbonImmutable $month, CarbonImmutable $today): void
    {
        // [日, 種別, カテゴリ, 金額, メモ]
        $rows = [
            [25, 'income', '給与', 280000, '月給'],
            [10, 'income', '副収入', 15000, 'フリマ売上'],

            [1, 'expense', '住居費', 78000, '家賃'],
            [5, 'expense', '水道光熱費', 9800, '電気・ガス'],
            [5, 'expense', '通信費', 7200, 'スマホ・光回線'],
            [3, 'expense', '食費', 3200, 'スーパー'],
            [6, 'expense', '食費', 4100, 'スーパー'],
            [9, 'expense', '食費', 2800, 'ランチ'],
            [12, 'expense', '食費', 5400, 'スーパー'],
            [16, 'expense', '食費', 3600, '友人と食事'],
            [20, 'expense', '食費', 4800, 'スーパー'],
            [4, 'expense', '日用品', 1980, 'ドラッグストア'],
            [14, 'expense', '日用品', 2650, '洗剤など'],
            [2, 'expense', '交通費', 5500, '定期券'],
            [18, 'expense', '交通費', 1200, 'タクシー'],
            [8, 'expense', '趣味・娯楽', 1980, '映画'],
            [22, 'expense', '趣味・娯楽', 3300, '書籍'],
            [15, 'expense', '交際費', 6000, '飲み会'],
            [11, 'expense', '衣服・美容', 8900, 'シャツ'],
            [19, 'expense', '医療費', 2400, '通院'],
        ];

        foreach ($rows as [$day, $type, $categoryName, $amount, $memo]) {
            $date = $month->day(min($day, $month->daysInMonth));

            // まだ来ていない日付は作らない（今月の未来分を避ける）
            if ($date->greaterThan($today)) {
                continue;
            }

            $category = Category::where('name', $categoryName)->where('type', $type)->firstOrFail();

            Transaction::create([
                'date' => $date->toDateString(),
                'type' => $type,
                'category_id' => $category->id,
                'amount' => $amount,
                'memo' => $memo,
            ]);
        }
    }
}
