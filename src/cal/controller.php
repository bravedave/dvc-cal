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

use strings;

class controller extends \Controller {
  protected $viewPath = __DIR__ . '/views/';

	protected function postHandler() {
    $action = $this->getPost('action');

		if ( 'gobble-di-gook' == $action) {
      Json::ack( $action);

    }
    else {
      parent::postHandler();

    }

  }

}