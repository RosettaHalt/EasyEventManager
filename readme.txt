=== Easy Event Manager ===

Contributors: halt2965
Donate link: http://web.lugn-design.com/
Tags: calendar, event, event calendar, event management, event registration, events, events calendar, manage, manager, easy, easy event, simple, simple event
Requires at least: 3.0.0
Tested up to: 3.3.1
Stable tag: 0.7.1

Easy to manage for event calendar.

== Description ==
Easy to manage for event calendar.<br />
シンプルで簡単なイベントの管理プラグインです。

== Installation ==

1. Upload `EasyEventManager` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. Place add code in your templates. Please refer to "Arbitrary section" or "DashBoard -> Event Manager -> Documentation".

詳しくは<br />
DashBoard -> 設定 -> Event Manager -> Documentation<br />
に記載してあります。<br />
もしくはプラグインのページを参照<br />
http://goo.gl/xxZCE


== Frequently Asked Questions ==
 

== Changelog ==

= 0.7 =
公開

== Upgrade Notice ==
 

== Arbitrary section ==
テンプレート内で
`
$event_data = e2m_getEventData();
$data = $event_data[0];
`

とするとイベントのデータを取得できます。<br />
それぞれ整数、多重配列の型になっています。<br />
`
for($i = 0; $i < $total_event; $i++){
	$title_value[$i] = $event_data["title"][$i];
}
`
などのようにループで処理すれば登録したデータを取得することができます。<br />
後はカレンダーに組み込んだり、リスト形式で表示するなど自由に使うことができます。<br />

今現在、取得できる情報は年、月、日、曜日、日付(yy/mm/dd形式)、タイトル、URL、その他(自由欄)です。<br />

デバックなどで配列の中身を見たい時は
`e2m_showDebugData();`
を呼べば中身をprint_r()で表示することが出来ます。<br />

詳しくは<br />
DashBoard -> 設定 -> Event Manager -> Documentation<br />
に記載してあります。<br />
もしくはプラグインのページを参照<br />
http://goo.gl/xxZCE

<br />
今後のこと<br />
とりあえず動くものが必要でしたのでソースがとんでもねぇ酷さ。要改善。<br />
<br />
実装予定<br />
独立したカテゴリ機能(検討中)<br />
カレンダー形式での出力<br />
リスト形式での出力<br />
出力時のタグをユーザーが選べるようにする(検討中)<br />
カテゴリー機能の追加(検討中)
