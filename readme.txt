=== Easy Event Manager ===

Contributors: halt2965
Donate link: http://web.lugn-design.com/
Tags: 
Requires at least: 3.0.0
Tested up to: 3.3
Stable tag: 1.2

Easy to manage events.

== Description ==
> Easy to manage events. <br />
> シンプルで簡単なイベントの管理プラグインです。

== Installation ==

1. Upload `EasyEventManager` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. Place add code in your templates. Please refer to "Arbitrary section".


== Frequently Asked Questions ==
 

== Changelog ==

= 0.3 =
公開

== Upgrade Notice ==
 

== Arbitrary section ==
> テンプレート内で
>> $total_event = getTotalEvent();<br />
>> $event_data = getEventData(); <br />

> とするとイベントの総数、イベントのデータを取得できます。<br />
> それぞれ整数、多重配列の型になっています。<br />
>> for($i = 0; $i < $total_event; $i++){ $title_value[$i] = $event_data["title"][$i]; } <br />

> などのようにループで処理すれば登録したデータを取得することができます。<br />
> 後はカレンダーに組み込んだり、リスト形式で表示するなど自由に使うことができます。<br />

> 今現在は
>> タイトル :	$event_data["title"]<br />
>> URL:		$event_data["url"]<br />
>> 年 : 		$event_data["year"]<br />
>> 月 : 		$event_data["month"]<br />
>> 日 : 		$event_data["days"]<br />
>> その他 : 	$event_data["other"]<br />

> 以上の配列値を取得できます。<br />

> デバックなどで配列の中身を見たい時は
>> showDebugData();

> を呼べば中身をprint_r()で表示することが出来ます。<br />

> <br />
> 今後のこと<br />
> もうちょい管理画面見やすくします。<br />
> イベント数が多くなると管理がしにくいと思うのでその辺りもなんとかしようとは思っています。<br />
> 今はとりあえず動くものが必要でしたので。
> ソースもとんでもねぇ酷さ。要改善。

> 実装予定
> 日付でのソート
> 追加、編集、削除をタブでの管理(検討中)
> 独立したカテゴリ機能(検討中)
> 削除の最適化