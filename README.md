# PHP `foreach` 参照残留デバッグラボ

この最小再現プロジェクトは、参照付き `foreach` のループ変数を後続の通常の `foreach` で再利用したとき、**読み取り専用のつもりのループが配列の末尾要素を上書きする**PHP固有の挙動を再現する。

## 前提環境

| 項目 | 固定内容 |
|---|---|
| PHP | **8.3.6 CLI** |
| 外部依存 | なし |
| 実行環境 | Linux / macOS / Windows の PHP CLI |

## ディレクトリ構成

```text
.
├── src/OrderStatusNormalizer.php  # 不具合のある業務風メソッド
├── tests/run.php                  # 外部依存のない振る舞いテスト
└── evidence/                      # 実行結果の保存先
```

## 不具合の再現

次のコマンドを実行する。

```bash
php tests/run.php
```

初期状態では、正規化だけを実行する対照テストは通る一方、後続の監査ループを追加したテストが失敗する。実際には、`order-300` が `order-200` と同じレコードに変わる。

```text
Expected: order-300 / settled
Actual:   order-200 / pending
```

## 修正後の確認

参照付きループの直後に `unset($order);` を置くと、同じコマンドで全テストが成功する。詳細な観測・仮説比較・最小修正は、同梱する技術記事下書きで扱う。
