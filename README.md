# bbs
認証機能付きの掲示板です

![](https://github.com/inooooo/bbs/blob/master/screenshot.png)

# DEMO
[デモ動画](https://www.youtube.com/watch?v=TLU0xYyxvPQ)

# テーブル

### user_data（ユーザの登録情報を入れる）

| Field             | Type      | Null | Key | Default | Extra |
|-------------------|-----------|------|-----|---------|-------|
| id                | char(8)   | YES  |     | NULL    |       |
| nickname          | char(15)  | YES  |     | NULL    |       |
| password          | char(128) | YES  |     | NULL    |       |
| registration_date | char(11)  | YES  |     | NULL    |       |
| gender            | int(11)   | YES  |     | NULL    |       |
| self_introduction | text      | YES  |     | NULL    |       |
| web_url           | text      | YES  |     | NULL    |       |
| img_url           | text      | YES  |     | NULL    |       |


### thread_list（作成されたスレッド一覧を入れる）
| Field              | Type    | Null | Key | Default | Extra |
|--------------------|---------|------|-----|---------|-------|
| thread_title       | text    | YES  |     | NULL    |       |
| registration_time  | int(11) | YES  |     | NULL    |       |
| last_modified_time | int(11) | YES  |     | NULL    |       |

### [thread名]　（スレッドごとの情報を入れる）　※スレッドが作成された時に作成される
| Field      | Type     | Null | Key | Default | Extra |
|------------|----------|------|-----|---------|-------|
| id         | char(10) | YES  |     | NULL    |       |
| write_time | char(20) | YES  |     | NULL    |       |
| content    | text     | YES  |     | NULL    |       |
