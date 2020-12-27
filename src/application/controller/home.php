<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use dvc\cal\reader;

class home extends dvc\cal\controller {
  protected function _index() {
    $path = implode( DIRECTORY_SEPARATOR, [
      config::dataPath(),
      'feed.ics'

    ]);

    $reader = reader::readICS( $path);
    $start = date( 'Y-m-d');
    $end = date( 'Y-m-d', strtotime( '+7 days'));
    $this->data = (object)[
      'start' => $start,
      'end' => $end,
      'feed' => $reader->feed( $start, $end)

    ];

    $this->render([
      'primary' => 'agenda',
      'secondary' => 'blank'

    ]);

  }

}
