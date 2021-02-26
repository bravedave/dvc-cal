<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class currentUser extends dvc\currentUser {
  static function getCalendarCredentials() {
    $file = implode( DIRECTORY_SEPARATOR, [
      rtrim( config::dataPath(), '/ '),
      'caldav-currentuser.json'

    ]);

    if ( file_exists( $file)) {
      return json_decode( file_get_contents( $file));

    }

    return null;

  }

}
