<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class config extends dvc\config {

  static protected function _cal_feeds_file() {
		return implode( DIRECTORY_SEPARATOR, [
      rtrim( self::dataPath(), '/ '),
      'cal_feeds.json'

    ]);

  }

  static public function cal_feeds() : array {
		if ( file_exists( $feeds = self::_cal_feeds_file())) {
      return (array)Json::read( $feeds);

    }
    else {
      $feed = [
        (object)[
          'name' => 'Sample Feed',
          'url' => '/',
          'color' => '#ffcdd2',
          'method' => 'POST'

        ]

        ];

      Json::write( $feeds, (object)$feed);
      return $feed;

    }

    return [];

  }

}