<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class caldav extends Controller {
  protected function _index() {
    $config = implode( DIRECTORY_SEPARATOR, [
      config::dataPath(),
      'caldav.json'

    ]);

    if ( file_exists( $config)) {
      $settings = (array)json_decode( file_get_contents( $config));
      $client = new dvc\cal\dav\client( $settings);

      $calendars = [];
      $events = [];
      // printf( '<br>principal : %s', $client->principal);

      if ( $calendar = $client->getCalendar( 'Personal')) {
        $from = date( 'Y-m-d', strtotime( 'last monday'));
        $to = date( 'Y-m-d', strtotime( '+2 day'));
        $_events = $client->getEvents( $calendar, $from, $to);

        // \sys::dump( $_events, 'Personal Calendar');

        $events = [];
        foreach ($_events as $_event) {
          $reader = dvc\cal\reader::readICS( $_event->data);
          $feed = $reader->feed( $from, $to);
          foreach ($feed as $e) {
            $events[] = $e;

          }

        }

        \sys::dump( $events, 'Personal Calendar', false);

      }
      else {
        \sys::dump( $client->getCalendars(), 'Calendars', false);

      }

      // $features = $client->options();
      // \sys::dump( $features, null, false);

      /**
       * Creating a calendar object
       */
      $tz = new DateTimeZone( config::$TIMEZONE);
      $vcalendar = new \Sabre\VObject\Component\VCalendar([
          'VEVENT' => [
              'SUMMARY' => 'Birthday party!',
              'DTSTART' => new \DateTime( date( 'Y-m-d') . ' 17:00:00', $tz),
              'DTEND'   => new \DateTime( date( 'Y-m-d') . ' 23:00:00', $tz)
          ]
      ]);

      // \sys::dump( $vcalendar, null, false);

      if ( $calendar = $client->getCalendar( 'Personal')) {

        \sys::dump( $calendar, null, false);

        $response = $client->createEvent( $calendar, $vcalendar);
        \sys::dump( $response, 'create entry', false);

      }

      /**
       * Get today’s events.
       */

      print '<br>done';

    }
    else {
      print '<br>config file missing';

    }

  }

}