# FFXIV Scheduler

FF14向けのスケジュール調整ツールです。固定・アライアンス・クレセントアイルなどのパーティ構成に対応した日程調整ができます。

## 機能

- イベント作成（パーティタイプ・ロール構成・日程・参加可否形式を設定）
- 参加登録（ジョブ選択・スロットごとの出欠・補足メモ）
- 回答一覧グリッド表示（集計付き）
- 管理者トークンURLによるイベント編集・削除
- クッキーによる参加者識別
- クレセントアイル専用サポートジョブ選択

## 技術スタック

- PHP 8.4 / Laravel 11
- SQLite（開発） / MariaDB（本番）
- Vite + Tailwind CSS v4 + SASS
- Alpine.js

## ローカル開発（GitHub Codespaces）

リポジトリをCodespacesで開くと `.devcontainer/setup.sh` が自動実行されます。

```bash
php artisan serve
```

でアクセスできます。

## 本番デプロイ（スターサーバー）

`main` ブランチへのpushでGitHub Actionsが自動デプロイします。

### 必要なSecrets

| 名前 | 内容 |
|---|---|
| `FTP_SERVER` | FTPホスト名 |
| `FTP_USERNAME` | FTPユーザー名 |
| `FTP_PASSWORD` | FTPパスワード |

### 初回セットアップ（SSHで実行）

```bash
cd /path/to/ffxivsch
touch database/database.sqlite  # SQLiteの場合のみ
/usr/bin/php8.4 artisan key:generate
/usr/bin/php8.4 artisan migrate --seed --force
/usr/bin/php8.4 artisan storage:link
```

### `.env` の主要設定

```
APP_KEY=
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ffxivsch.quartzer.blue
APP_LOCALE=ja

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=file
```
