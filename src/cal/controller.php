<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\cal;

use currentUser;
use Json;
use strings;

class controller extends \Controller {
  protected $viewPath = __DIR__ . '/views/';

  protected $feeds = [];  // unless you populate this it won't work ...

	protected function _index() {
    $start = date( 'Y-m-d');
    $end = date( 'Y-m-d', strtotime( '+7 days'));
    $this->data = (object)[
      'start' => $start,
      'end' => $end,
      'feed' => []

    ];

    $this->render([
      'primary' => 'calendar',
      'secondary' => 'feeds',
      'data' => (object)[
        'searchFocus' => false,

      ],

    ]);

  }

	protected function postHandler() {
    $action = $this->getPost('action');

		if ( 'get-active-feeds' == $action) {
      $a = [];
      foreach ($this->feeds as $feed) {
        if ( 'yes' == currentUser::option( 'cal-feed-' . $feed->name)) {
          $a[] = $feed;

        }

      }

      Json::ack( $action)
        ->add( 'data', $a);

    }
		elseif ( 'get-feed' == $action) {
      $name = $this->getPost('name');
      if ( 'Sample Feed' == $name) {
        $path = implode( DIRECTORY_SEPARATOR, [
          config::dataPath(),
          'feed.ics'

        ]);

        $start = $this->getPost('start');
        $end = $this->getPost('end');

        // $reader = reader::readICS( $path);
        $reader = reader::ICSString( file_get_contents( $path));
        $feed = $reader->feed( $start, $end);

        Json::ack( $action)
          ->add( 'data', $feed);

      } else { Json::nak( sprintf( '%s - %s', $name, $action)); }

    }
		elseif ( 'toggle-feed' == $action) {
      if ( $feed = $this->getPost('feed')) {
        $state = $this->getPost('state');

        currentUser::option( 'cal-feed-' . $feed, 'yes' == $state ? 'yes' : '');
        Json::ack( $action);

      } else { Json::nak( $action); }

    }
    else {
      parent::postHandler();

    }

  }

  public function agenda() {
    $seed = $this->getParam( 'seed');
    if ( strtotime( $seed) < 1) {
      $seed = date( 'Y-m-d');

    }

    $this->data = (object)[
      'seed' => $seed

    ];

    $this->load( 'agenda');

  }

  public function month() {
    $seed = $this->getParam( 'seed');
    if ( strtotime( $seed) < 1) {
      $seed = date( 'Y-m-d');

    }

    $time = strtotime( $seed);
    $seed = date( 'Y-m-01', $time);

    $this->data = (object)[
      'seed' => $seed

    ];

    $this->load( 'month');

  }

  public function week() {
    $seed = $this->getParam( 'seed');
    if ( strtotime( $seed) < 1) {
      $seed = date( 'Y-m-d');

    }

    $time = strtotime( $seed);
    if ( date('w', $time)) {
      $seed = date( 'Y-m-d', strtotime( 'Last Sunday', $time));

    }

    $this->data = (object)[
      'seed' => $seed

    ];

    $this->load( 'week');

  }

}