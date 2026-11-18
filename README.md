# 家計簿アプリ（Laravel）

月ごとに収入・支出・収支と、支出のカテゴリ別内訳、明細を確認できる家計簿アプリです。
このフォルダは **Laravel 11 / 12 のプロジェクトに重ねて使うファイル一式** です（Laravel本体は含みません）。

## セットアップ

```bash
# 1. Laravel プロジェクトを作成（PHP 8.2+ / Composer が必要）
composer create-project laravel/laravel kakeibo
cd kakeibo

# 2. このフォルダの中身を、作成したプロジェクトにコピーして上書き
#    （app/ database/ resources/ routes/ の4つ）

# 3. マイグレーションと初期カテゴリの投入
php artisan migrate
php artisan db:seed --class=CategorySeeder

# 4. 起動
php artisan serve
```

ブラウザで http://localhost:8000 を開くと、今月の収支が表示されます。
Laravel 11 以降はデフォルトで SQLite なので、DB の設定は不要です（MySQL などを使う場合は `.env` を編集）。

## 機能

| 画面 | URL | 内容 |
|---|---|---|
| 月別収支 | `/months/2026-11` | 収入・支出・収支、支出の内訳、明細一覧、前月/翌月の切り替え |
| 記録 | `/transactions/create` | 収入/支出の登録（種別に合うカテゴリだけ選択可） |
| 編集・削除 | `/transactions/{id}/edit` | 明細の修正、削除 |

## 構成

- `database/migrations/` … `categories`、`transactions` テーブル
- `app/Models/Transaction.php` … `scopeInMonth()` で月単位の絞り込み
- `app/Http/Controllers/MonthlyController.php` … 月別の集計
- `app/Http/Controllers/TransactionController.php` … 登録・編集・削除
- `app/Http/Requests/TransactionRequest.php` … 入力チェック（種別とカテゴリの整合性も検証）
- `resources/views/` … Blade（Tailwind は CDN 読み込みのためビルド不要）

## 設計メモ

- 金額は円の整数（`unsignedInteger`）で保存し、収入/支出は `type` 列で区別しています。
- 現状はログインなしの1人用です。複数人で使う場合は Laravel Breeze 等で認証を入れ、`transactions` に `user_id` を追加してください。
- 次の拡張候補：年間の月別推移グラフ、カテゴリ管理画面、予算設定、CSVエクスポート。
