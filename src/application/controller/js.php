<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use dvc\jslib;

class js extends Controller {
	public function cal() {
    jslib::viewjs([
      'debug' => false,
      'libName' => 'dvc-cal',
      'jsFiles' => sprintf( '%s/app/js/*.js', $this->rootPath ),
      'libFile' => config::tempdir()  . '_cal_tmp.js'

    ]);

  }

}
