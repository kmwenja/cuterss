<?php

require_once('simplepie/autoloader.php');

$feed = new SimplePie();

$feed->set_feed_url('./rss.xml');

