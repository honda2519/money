# Render で公開する手順

## 1. ファイルを自分のプロジェクトに追加する

この zip の中身を `C:\dev\kakeibo-app` に**そのままコピー**します（上書き確認が出たら「はい」）。

追加されるもの:

- `Dockerfile` … Render で動かすための設定
- `.dockerignore` … イメージに含めないファイル
- `docker/vhost.conf`, `docker/entrypoint.sh` … Apache の設定と起動スクリプト
- `database/seeders/DemoSeeder.php` … デモ用の架空データ（直近3か月分）

## 2. GitHub に反映する

```powershell
cd C:\dev\kakeibo-app
git add .
git commit -m "Render公開用の設定を追加"
git push
```

## 3. APP_KEY を作る

```powershell
php artisan key:generate --show
```

`base64:...` で始まる文字列が表示されます。これを手順4で使います（他人に見せない）。

## 4. Render で Web サービスを作る

1. https://render.com に GitHub アカウントでサインアップ
2. **New → Web Service** → GitHub の `kakeibo` リポジトリを選ぶ
3. 次のとおり設定する
   - **Language**: Docker
   - **Instance Type**: Free
4. **Environment Variables** に次を追加する

| Key | Value |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | 手順3の文字列 |
| `APP_URL` | 作成後に表示される公開URL（最初は空でもよい） |
| `DB_CONNECTION` | `sqlite` |
| `DB_DATABASE` | `/var/www/html/database/database.sqlite` |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_STORE` | `file` |
| `LOG_CHANNEL` | `stderr` |

5. **Create Web Service** を押す。ビルドに数分かかります。
6. 完了後、`https://〜.onrender.com` を開く。

## 5. うまくいかないとき

Render の画面の **Logs** に出ているエラーを貼ってください。
