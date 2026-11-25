# 商品・在庫・注文管理システム（Laravel ポートフォリオ）

フリマ取引・EC出品における**商品在庫・変更履歴・注文状況・売上**を一元管理するための Web アプリケーションです。  
※本リポジトリはポートフォリオ用のデモアプリであり、データベース内のシードデータ（ダミーデータ）のみを扱っています。外部サービスへのスクレイピングや非公式 API アクセス等は一切行っておりません。

---

## 主な機能

- **認証・ゲストログイン**
  - クローズド運用を想定し、新規登録（`/register`）およびパスワードリセット（`/password/reset`）を無効化
  - 動作確認用のゲストログイン機能（`ALLOW_GUEST_LOGIN` 環境変数で有効/無効を切り替え可能）
- **商品・在庫管理**
  - 商品一覧・複合検索（商品 ID / 商品名 / 価格 / 在庫数、ページネーション対応）
  - 新規商品登録（類似商品名の重複チェック・確認モーダル、暗号学的乱数 `random_int` によるユニーク ID 付与）
  - 商品詳細閲覧・情報編集・在庫数の加算・論理削除（`del_flg` 管理）
- **操作履歴（監査ログ）**
  - 登録・編集・在庫追加・削除の各操作を `histories` テーブルへ自動記録（操作者名はセッションの認証済みユーザーから取得）
- **注文管理**
  - 注文一覧・検索（商品 ID / 商品名 / 購入者名 / 購入日）
  - 処理区分（未到着 / 到着済み / 返品）ごとのタブ切り替え・ページネーション
- **売上チャート**
  - Google Charts を用いた商品別売上数の円グラフ表示

---

## 技術スタック

| カテゴリ | 技術 |
| :--- | :--- |
| バックエンド | PHP 8.1 / Laravel 9 |
| データベース | MariaDB 10.6（テスト実行時は SQLite インメモリ） |
| Web サーバー | Nginx |
| フロントエンド | Blade / Bootstrap 4 / Vue 2 / Google Charts / Laravel Mix |
| コンテナ | Docker / Docker Compose |
| テスト | PHPUnit 9（Feature テスト / セキュリティテスト） |

---

## 環境構築（Docker Compose）

### 1. リポジトリのクローンと環境変数ファイルの作成

```bash
git clone <リポジトリURL>
cd mercari_system
cp .env.example .env
```

### 2. コンテナの起動

```bash
docker compose up -d --build
```

### 3. 依存パッケージのインストール・アプリケーション初期化

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

ブラウザで `http://localhost:8080` にアクセスしてください（phpMyAdmin は `http://localhost:8081`）。

> **補足**: ゲストログインを利用する場合は、ID `2` のユーザー（または `.env` の `GUEST_USER_ID` で指定した ID のユーザー）が `users` テーブルに存在する必要があります。必要に応じて `php artisan tinker` 等でテスト用ユーザーを作成してください。

---

## テストの実行

`phpunit.xml` でテスト用 DB として SQLite（`:memory:`）が設定されているため、DB コンテナへの影響なくテストを実行できます。

```bash
./vendor/bin/phpunit
```

Docker コンテナ内で実行する場合:

```bash
docker compose exec app ./vendor/bin/phpunit
```

### 主なテスト内容

- **`tests/Feature/MercariControllerTest.php`**: 未認証リダイレクト、一覧・LIKE 検索、詳細/削除済み商品の 404 制御、編集時の監査ログ整合性（リクエスト上の操作者名偽装の無効化）、POST 削除、在庫加算、商品登録時の関連レコード生成、バリデーション、XSS エスケープ、LIKE ワイルドカード文字のエスケープ
- **`tests/Feature/SecurityTest.php`**: 新規登録・パスワードリセットルートの無効化（404）、ゲストログインの有効/無効制御（403）、未許可HTTPメソッドアクセスの遮断（405）

---

## セキュリティ・品質面で意識した点

- **SQL LIKE 検索のワイルドカードエスケープ**: `%` や `_` を入力された場合でも意図しない全件一致を防ぐためエスケープ処理を実装（SQLite / MySQL 両対応）。
- **監査ログの改ざん防止**: 履歴テーブルへの操作者名記録時、リクエストパラメータを信用せず `Auth::user()->name` を参照。
- **XSS 対策**: チャート描画用の JS 埋め込み時に `JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP` オプション付きで `json_encode` を実施。
- **Web サーバー / コンテナのハードニング**:
  - `nginx/default.conf` にセキュリティヘッダ（`X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`）および隠しファイル（`.env`, `.git` 等）アクセス拒否を設定。
  - `docker-compose.yml` で公開ポートを `127.0.0.1` に限定バインドし、PHP-FPM（9000）はコンテナ間通信のみに制限。
