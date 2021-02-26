<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\cal\dav;

use currentUser;
use dvc\cal\config;
use dvc\ews\calendar;
use dvc\ews\calendaritem;

class appointment {
  static public function create( calendaritem $app, $creds = null) {

    if ( !$creds) {
      $creds = currentUser::getCalendarCredentials();

    }

    if ( $creds) {

      $o = (object)$creds;
      if ( isset( $o->baseUri)) {

        /** Creating a calendar object */

        $tz = new \DateTimeZone( config::$TIMEZONE);
        $event = [
          'SUMMARY' => $app->subject,
          'DESCRIPTION' => $app->notes,
          'DTSTART' => new \DateTime( $app->startUTC, $tz),
          'DTEND'   => new \DateTime( $app->endUTC, $tz)

        ];

        if ( $app->location) {
          $event['LOCATION'] = $app->location;

        }

        $vcalendar = new \Sabre\VObject\Component\VCalendar(['VEVENT' => $event]);

        $client = new client( (array)$creds);
        if ( $calendar = $client->getCalendar( 'Personal')) {
          \sys::logger( sprintf('<%s> %s', 'create event in personal calendar', __METHOD__));
          return $client->createEvent( $calendar, $vcalendar);

        }
        else {
          \sys::logger( sprintf('<%s> %s', 'could not find personal calendar', __METHOD__));

        }

        \sys::logger( sprintf('<%s> %s', print_r( $app, true), __METHOD__));
        return false;

      }

    }

    return calendar::CreateAppointment( $app, $creds);

  }

}