# slack-sms

Raspberry PIで4GPIとかをインストールして、受信したSMSをSlackに流すだけのPHP。
会社共通のGoogleアカウントとかが二要素認証のおかげで運用がめちゃくちゃ面倒くさい。
Twillioとかだと国内番号取得できないし、規約でそういう使い方ダメみたい。

# 手順

## ハードウェアとOSの設定

Raspberry PIで4GPIとかSoracom Airとかを購入して、セッティングしてください。
https://qiita.com/vascodagama/items/114619c2bcf020226cd7

## SIMの設定

一度設定してください。APNとかの設定はSIMの発行元の会社が公開してるはず。

````
sudo nmcli con add type gsm ifname "*" con-name 「接続名」 apn 「ＡＰＮ」 user 「ユーザー」 password 「パスワード」
````

## PHPとかのミドルウェアインストール

````
apt install php-cli php-curl composer supervisor
````

## slack-smsを配置

/home/pi/slack-smsにgithubからソースコード一式を置く。

## 設定

/home/pi/slack-sms/bin/config.phpをいじる。

Slack Incoming Webhook URLの取得方法

https://slack.com/intl/ja-jp/help/articles/115005265063-Slack-%E3%81%A7%E3%81%AE-Incoming-Webhook-%E3%81%AE%E5%88%A9%E7%94%A8

## Composer Install

cd /home/pi/slack-sms
composer install

## supervisorの設定

/etc/supervisor

````
[program:slack-sms]
command=/usr/bin/php /home/pi/slack-sms/bin/slack-sms.php
user=pi
autostart=true
autorestart=true
startsecs=1
````

# 動作確認

SIMのSMS電話番号にメッセージを送ると、Slackに投稿されるはず。
