<?php

require_once('simplepie/autoloader.php');

$feed = new SimplePie();

$feed->set_feed_url('./rss.xml');

$feed->init();

$feed->handle_content_type();

echo $feed->get_title();

echo "<br/>";

echo $feed->get_description();

echo "<br/>";

foreach($feed->get_items() as $item){
	echo "{$item->get_title()}: {$item->get_description()}";
	echo "<br/>";
}
