# Twitter バックアップツール

Twitter のユーザーやツイートのバックアップを手助けするツールです。

もともと自分用に作っていたものなため、注意事項を熟読の上、ご使用ください。

bash を使えることを前提としています。

## 1. バックアップの流れ

1. php と Twitter API (または JavaScript によるブックマークレット) を使用してツイートやユーザーを取得。
2. php でツイートやユーザーを HTML で出力。
3. wget で画像や動画などのメディアを含めて保存。

※ php は `php -S 0.0.0.0:8080` などでローカルサーバーを建てて使用することを想定しています。

## 2. 本ツールで保存できるもの

※ `[ログイン]`: 本ツールから Twitter にログインするのに使用する識別子。

- ツイート取得 (主に他人の)
	- ツイート単体
		- `/tweet/[ログイン]/[ツイート ID]/`
	- ユーザーのツイート一覧
		- `/tweets-3200/[ログイン]/[@ユーザー名]/`
	- ユーザーのメディアツイート一覧
		- `/media-3200/[ログイン]/[@ユーザー名]/`
- ツイート取得 (主に自分の)
	- いいね
		- `/favorites/[ログイン]/[@ユーザー名]/`
	- ブックマーク
		- `/bookmarks/[ログイン]/` (※別途、JSON 形式の入力ファイルが必要)
	- モーメント
		- `/moments/[ログイン]/` (※別途、JSON 形式の入力ファイルが必要)
- ユーザー取得 (主に自分の)
	- フォロー中
		- `/following/[ログイン]/[@ユーザー名]/`
	- フォロワー
		- `/followers/[ログイン]/[@ユーザー名]/`
	- リスト
		- `/lists/[ログイン]/[@ユーザー名]/`

## 3. 使い方

### 3.1. 必要なもの

- bash
- php
- wget
- Twitter API
- WEB ブラウザ

### 3.2. Twitter API と連携

Twitter API の取得と連携は各自でお願いします。

API 用のキー 2 つとユーザー用のトークン 2 つを、本ツールの設定ファイル `/dist/inc/config.json` を作成して追加します。

以下の例では、`username_main` や `username_sub` が `[ログイン]` に相当します (@ユーザー名である必要はないですが、@ユーザー名と同じにした方が分かりやすいと思います) 。

```json
{
	"api_key"       : "XXXXXXXXXXXXXXXXXXXXXXXXX",
	"api_secret_key": "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
	"profiles": {
		"username_main": {
			"access_token"       : "000000000000000000-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
			"access_token_secret": "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
		},
		"username_sub": {
			"access_token"       : "0000000000000000000-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
			"access_token_secret": "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
		}
	}
}
```

アプリ連携用のソースコードを書かずに、サブアカウントでアクセストークンとアクセストークンシークレットを取得したい場合は、以下のページを参考にしてください。

参考「[Twurl で連携アプリ認証し、アクセストークンとシークレットを取得 - Qiita](https://qiita.com/kerupani129/items/8a144d3c152b4f4708a9)」

### 3.3. ブックマークレットの実行 (ブックマークまたはモーメントのみ)

1. WEB 版 Twitter をブラウザで開く。
2. ブックマークまたはモーメントのページの一番上の状態で、以下の「ツイート取り込み開始ブックマークレット」を実行する (ブックマーク・モーメント共通) 。
3. 少しずつページを一番下までスクロールしていく。
4. ページ一番下まで来たら、以下の「ツイート取り込み終了ブックマークレット」を実行する (ブックマーク・モーメント別) 。
5. JSON 形式でツイートが保存される。
6. 必要に応じて `/dist/moments/` または `/dist/bookmarks/` ディレクトリに JSON ファイルを移動する。
	- モーメントの場合は、各モーメントを別々に保存します。保存したいモーメントをそれぞれ全て保存してください。
	- ファイル名をもとに php から読み込むので、ファイル名を変更しないでください。

「ツイート取り込み開始ブックマークレット」(ブックマーク・モーメント共通)

```js
javascript:(()=>{const s=document.createElement('script');s.src='https://kerupani129s.github.io/twitter-backup-tool/o.js';document.head.appendChild(s);})();
```

「ツイート取り込み終了ブックマークレット」(ブックマーク用)

```js
javascript:(()=>{const s=document.createElement('script');s.src='https://kerupani129s.github.io/twitter-backup-tool/sb.js';document.head.appendChild(s);})();
```

「ツイート取り込み終了ブックマークレット」(モーメント用)

```js
javascript:(()=>{const s=document.createElement('script');s.src='https://kerupani129s.github.io/twitter-backup-tool/sm.js';document.head.appendChild(s);})();
```

### 3.4. ローカルサーバーで php を実行し、WEB ブラウザで確認

`/dist/` ディレクトリで `php -S 0.0.0.0:8080` などでローカルサーバーを建て、WEB ブラウザ上で表示を確認します。

- 例: `http://localhost:8080/favorites/username/username/`

### 3.5. wget で保存

以下のようなオプションを用いて、wget で画像や動画などを含めて保存することができます。

```bash
wget --page-requisites \
    --span-hosts \
    --quiet --show-progress \
    --convert-links \
    --adjust-extension \
    --execute robots=off \
    --restrict-file-names=windows \
    --no-directories \
    --directory-prefix=favorites \
    http://localhost:8080/favorites/username/username/
```

## 4. 注意事項

- 全般
	- 使用は自己責任でお願いします。
	- ログインユーザーを間違えると、非公開ツイートなどが正しく取得できないことがあります。
	- 自分用にローカルで動作させることを想定しているため、php のセキリティの対策はしていません。WEB 上に公開する際にはセキリティの対策をお願いします。
	- 同様の理由で、通信エラーやパース時のエラーなどもチェックしていません。必要に応じて追記してください。
	- ある程度テストしていますが完全ではないため、予期せぬバグを含んでいる可能性があります。
	- ツイートに関して、テキスト、画像、動画、GIF は対応していますが、投票は対応していません。
- ユーザーツイート一覧に関して (メディアツイート含む)
	- Twitter API の仕様上、最大約 3200 件までとなっています。
	- メディアツイートに関しては、その最大約 3200 件の中からメディアを含むものを取り出すため、さらに件数が少なくなります。
	- WEB 版 Twitter の日付検索と、ブックマーク用のブックマークレットを使用することで、ある程度回避できる可能性があります。
- ブックマークとモーメントに関して
	- ブックマークとモーメントは Twitter の公開 API で取得することができないため、JavaScript によるブックマークレットを用いて取得します。
	- DOM の解析が雑なため、Twitter が仕様変更すると使えなくなる可能性があります。
	- 一部ツイートが正しく保存されないことがあります。
		- 例：名前に絵文字を含むユーザーが画像に関連付けられている場合、その絵文字も画像の 1 つとして認識されてしまう。
	- ブックマークレットは日本語の WEB 版 Twitter を想定しており、他の言語では正しく取得できません。URL に `?lang=ja` を付けるなどして日本語版で表示するか、他の言語用にブックマークレットのソースコードの改変をお願いします。
- リストまたはモーメントの Total Count について
	- リストまたはモーメントの一番上に表示される Total Count は、リストやモーメントそのものの個数であり、ユーザーやツイートの合計数ではありません。
	- 各リストまたはモーメントのそれぞれに表示される Total Count は、リストまたはモーメントそれぞれのユーザーまたはツイートの数です。

## 5. ライセンス

[MIT License](LICENSE)

## 6. バージョン履歴

- 2020/04/07 ver.1.0
	- とりあえずユーザーとツイートを一通り保存できるように。

## 7. 今後の予定

- HTML の保存でなく JSON の保存を主体にしたい。
	- wget で HTML 中のメディアを保存するのではなく、JSON からメディアを保存できるようにする。
	- php で HTML を出力するのではなく、JavaScript で HTML を出力するようにする。
	- 全て JSON 形式で保存することで、より多くの情報をバックアップできるようになる。
	- JSON の保存、メディアの保存、ツイートの表示の処理をそれぞれ分離することができ、メンテナンス性が向上する。
- WEB 版 Twitter の日付検索と JavaScript によるブックマークレットを用いた、ツイート 3200 件問題の解決。
	- 実質、非公開検索 API を用いて取得するのと同意義。非公開 API を直接使用するよりこちらの方が大丈夫そう (？) 。
	- 日付検索を用いても、完全な解決は不可能 (Twitter の仕様で、検索時に一部ツイートが表示されないことがある) 。
	- プレミアム公開 API を使用することで完全な回避が可能。ただし、月額料金。
