<?php

session_start();

require_once('vendor/autoload.php');
require_once('simplepie/autoloader.php');

$app = new \Slim\Slim();

require_once('config.php');

class MyItemClass extends SimplePie_Item{

	public function get_image(){
		$doc = phpQuery::newDocument($this->get_description());
		$src = null;

		$img = $doc->find('img:first');
		$src = $img->attr('src');

		return $src;
	}
}

function get_feed_items(){
	// go through all the feed urls
	$items = array();

	$feeds = ORM::for_table('feeds')->find_many();
	foreach($feeds as $feed_loc){
		$feed = new SimplePie();
		
		$feed->set_feed_url($feed_loc->location);
		$feed->set_cache_location("mysql://feeds:feeds@localhost:3306/feeds");
		$feed->set_item_class('MyItemClass');
		$feed->init();
		$feed->handle_content_type();

		$items = array_merge($items, $feed->get_items());
	}

	return $items;
}

function index(){
	$app = \Slim\Slim::getInstance();

	$app->view()->appendData(array("items"=>get_feed_items()));

	$app->render('landing.twig.html');
}

function admin(){
	$app = \Slim\Slim::getInstance();

	$feeds = ORM::for_table('feeds')->find_many();
	$app->view()->appendData(array('feeds'=>$feeds));

	$app->render('admin.html.twig');
}

function add_rss_feed(){
	$app = \Slim\Slim::getInstance();

	$url = $app->request()->post('url');

	// TODO: check if url is empty

	// TODO: check if url points actual rss feed

	$new_feed = ORM::for_table('feeds')->create();
	$new_feed->location = $url;
	$new_feed->save();

	$app->redirect($app->urlFor('admin'));
}

function remove_rss_feed($id=null){
	$app = \Slim\Slim::getInstance();

	if($id!==null){
		$feed = ORM::for_table('feeds')->find_one($id);
		$feed->delete();
	}

	$app->redirect($app->urlFor('admin'));
}

function get_rss_feed_json(){
	$app = \Slim\Slim::getInstance();

	$feed = new SimplePie();
	$feed->set_feed_url('./rss.xml');
	$feed->init();
	$feed->handle_content_type();

	$app->render('feed.twig.json');
}

function get_rss_feed_html(){
	$app = \Slim\Slim::getInstance();

	$feed = new SimplePie();
	$feed->set_feed_url('./rss.xml');
	$feed->init();
	$feed->handle_content_type();

	$data = array();
	$data['feed'] = $feed;

	$app->view()->appendData($data);

	$app->render('feed.twig.html');
}

$app->get('/admin/remove/:id','remove_rss_feed')->name('remove_rss');
$app->post('/admin/add', 'add_rss_feed')->name('add_rss');
$app->get('/admin', 'admin')->name('admin');
$app->get('/feed', 'get_rss_feed_html')->name('feed');
$app->get('/', 'index')->name('landing');

$app->run();

?>
