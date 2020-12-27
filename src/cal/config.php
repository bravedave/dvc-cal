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

class config extends \config {
	const dvc_cal_db_version = 0;

  static protected $_DVC_CAL_VERSION = 0;

  static function dvc_cal_init() {
    $_a = [
      'dvc_cal_version' => self::$_DVC_CAL_VERSION,

    ];

		if ( file_exists( $config = self::dvc_cal_config())) {

      $j = (object)array_merge( $_a, (array)Json::read( $config));

      self::$_DVC_CAL_VERSION = (float)$j->dvc_cal_version;

		}

	}

}

config::dvc_cal_init();
