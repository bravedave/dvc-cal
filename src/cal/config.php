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

use Json;
class config extends \config {
	const dvc_cal_db_version = 0;

  static protected $_DVC_CAL_VERSION = 0;

  static protected function _dvc_cal_feeds_file() {
		return implode( DIRECTORY_SEPARATOR, [
      rtrim( self::dataPath(), '/ '),
      'dvc_cal_feeds.json'

    ]);

  }

  static public function dvc_cal_feeds() : array {
		if ( file_exists( $feeds = self::_dvc_cal_feeds_file())) {
      return (array)Json::read( $feeds);

    }
    else {
      $feed = [
        (object)[
          'name' => 'Sample Feed',
          'url' => '/',
          'method' => 'POST'

        ]

        ];

      Json::write( $feeds, (object)$feed);
      return $feed;

    }

    return [];

  }

  static function dvc_cal_init() {
    $_a = [
      'dvc_cal_version' => self::$_DVC_CAL_VERSION,

    ];

		if ( file_exists( $config = self::dvc_cal_config())) {

      $j = (object)array_merge( $_a, (array)Json::read( $config));

      self::$_DVC_CAL_VERSION = (float)$j->dvc_cal_version;

		}

	}

	static function dvc_cal_config() {
		return implode( DIRECTORY_SEPARATOR, [
      rtrim( self::dataPath(), '/ '),
      'dvc_cal.json'

    ]);

	}

}

config::dvc_cal_init();
