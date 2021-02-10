<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * https://sleeplessbeastie.eu/2018/06/11/how-to-display-upcoming-events-in-nextcloud-calendar-using-text-based-terminal-emulator/
 *
*/

namespace dvc\cal\dav;

use dvc\cal\config;
use Sabre;

class client {
  protected $_client;

  protected $_calendarRoot;

  protected $_calendars = [];

  public $principal;

  function __construct( array $settings) {
    $debug = false;
    // $debug = true;

    $this->_client = new Sabre\DAV\Client($settings);
    if ( $debug) {
      \sys::logger(
        sprintf(
          '<%s> %s',
          \application::timer()->elapsed(),
          __METHOD__

        )

      );

    }

    /**
     * Get a path for the user’s principal resource on the server.
     */
    if ( isset( $settings['principal'])) {
      $this->principal = $settings['principal'];

    }
    else {
      \sys::logger(
        sprintf(
          '<getting principal> <%s> %s',
          \application::timer()->elapsed(),
          __METHOD__

        )

      );

      if ( $props = $this->_client->propfind('', [ '{DAV:}current-user-principal' ], $depth = 0)) {
        $this->principal = $props['{DAV:}current-user-principal'][0]['value'];

        \sys::logger(
          sprintf(
            '<speed up by providing principal : %s> <%s> %s',
            $this->principal,
            \application::timer()->elapsed(),
            __METHOD__

          )

        );

      }

    }

    if ( $this->principal) {

      if ( $debug) \sys::logger( sprintf('<principal : %s> %s', $this->principal, __METHOD__));

      if ( isset( $settings['calendarRoot'])) {
        $this->_calendarRoot = $settings['calendarRoot'];

      }
      else {
        /**
         * Get a path that contains calendar collections
         * owned by the user using the user’s principal
         * resource address obtained in the previous step.
         **/

        $body = implode( '', [
          '<?xml version="1.0"?>',
          '<d:propfind xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">',
          '<d:prop><c:calendar-home-set /></d:prop>',
          '</d:propfind>'

        ]);

        $key = '{urn:ietf:params:xml:ns:caldav}calendar-home-set';

        if ( $response = $this->_client->request('PROPFIND', $this->principal, $body, [ 'Depth' => 0])) {
          // \sys::dump( $response);
          if ( 207 == $response['statusCode']) {
            if ( $multi = $this->_client->parseMultiStatus($response['body'])) {
              $first = \array_shift($multi);
              if ( isset( $first[200])) {

                if (isset( $first[200][$key])) {
                  $this->_calendarRoot = $first[200][$key][0]['value'];

                }
                elseif ( isset( $first[200]['{DAV:}href'])) {
                  $this->_calendarRoot = $first[200]['{DAV:}href'];

                }

                \sys::logger(
                  sprintf(
                    '<speed up by providing calendarRoot : %s> <%s> %s',
                    $this->_calendarRoot,
                    \application::timer()->elapsed(),
                    __METHOD__

                  )

                );

              }

            }

          }

        }

      }

    }

    if ( $debug) \sys::logger( sprintf('<calendarRoot : %s> %s', $this->_calendarRoot, __METHOD__));

    if ( isset( $settings['calendars'])) {
      $this->_calendars = (array)$settings['calendars'];

    }

  }

  public function getCalendar( $name) : ?object {
    $calendars = $this->getCalendars();

    if ( isset( $calendars[$name])) {
      return $calendars[$name];

    }

    return null;

  }

  public function getCalendars() : array {
    /**
     * Get calendar paths for the given collection.
     */

    if ( !$this->_calendars) {
      if ( $this->_calendarRoot) {

        if ( $a = $this->_client->propfind($this->_calendarRoot, [ '{DAV:}displayname' ], $depth = 1)) {
          foreach( $a as $_k => $_v) {
            if ( isset( $_v['{DAV:}displayname'])) {
              $_cal = (object)[
                'name' => $_v['{DAV:}displayname'],
                'path' => $_k,
                'todo' => false,
                'event' => false,

              ];

              /**
               * Get event types stored in this calendar e.g. VTODO, VEVENT.
               */
              $body = implode( '', [
                '<?xml version="1.0"?>',
                '<d:propfind xmlns:d="DAV:" xmlns:cal="urn:ietf:params:xml:ns:caldav">',
                '<d:prop><cal:supported-calendar-component-set/></d:prop>',
                '</d:propfind>'

              ]);

              $key = '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set';

              if ( $response = $this->_client->request('PROPFIND', $_cal->path, $body, [ 'Depth' => 0])) {
                if ( 207 == $response['statusCode']) {
                  if ( $multi = $this->_client->parseMultiStatus($response['body'])) {
                    $first = \array_shift($multi);
                    if ( isset( $first[200][$key])) {
                      foreach ($first[200][$key] as $element) {
                        if ( 'VEVENT' == $element['attributes']['name']) {
                          $_cal->event = true;

                        }

                      }

                    }

                  }

                }

              }

              $this->_calendars[ $_cal->name] = $_cal;

            }

          }

        }

      }

      \sys::logger(
        sprintf(
          '<speed up by providing calendars : %s> <%s> %s',
          \json_encode( $this->_calendars, JSON_UNESCAPED_SLASHES),
          \application::timer()->elapsed(),
          __METHOD__

        )

      );

    }

    return $this->_calendars;

  }

  public function getEvent( object $calendar, $uid) : ?object {

    $debug = false;
    // $debug = true;

    $path = $calendar->path . $uid;
    if ( $response = $this->_client->request('GET', $path)) {
      if ( 200 == $response['statusCode']) {

        return (object)[
          'calendar' => $calendar->name,
          'path' => $path,
          'uid' => $uid,
          'etag' => $response['headers']['etag'][0],
          'data' => $response['body'],

        ];

      }

    }

    if ( $debug) {
      \sys::logger(
        sprintf(
          '<%s event/s> <%s> %s',
          count( $events),
          \application::timer()->elapsed(),
          __METHOD__

        )

      );

    }

    return null;

  }

  public function getEvents( object $calendar, $from, $to) : array {

    $debug = false;
    // $debug = true;

    $start = new \DateTime( $from . ' 00:00:00');
    $start->setTimezone( new \DateTimeZone( 'UTC'));
    $start = $start->format('Ymd\THis');

    $end = new \DateTime( $to . ' 00:00:00');
    $end->setTimezone( new \DateTimeZone( 'UTC'));
    $end = $end->format('Ymd\THis');

    $filter = sprintf('<c:time-range  start="%s" end="%s"/>', $start, $end);

    if ( $debug) \sys::logger( sprintf( '<%s> <%s> %s', $filter, \application::timer()->elapsed(), __METHOD__));

    $body = implode( '', [
      '<?xml version="1.0"?>',
      '<c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">',
        '<d:prop><d:href /><d:getetag /><c:calendar-data /></d:prop>',
        '<c:filter>',
          '<c:comp-filter name="VCALENDAR">',
            sprintf( '<c:comp-filter name="VEVENT">%s</c:comp-filter>', $filter),
          '</c:comp-filter>',
        '</c:filter>',
      '</c:calendar-query>'

    ]);

    $events = [];
    $key = '{urn:ietf:params:xml:ns:caldav}calendar-data';
    if ( $response = $this->_client->request('REPORT', $calendar->path, $body, [ 'Depth' => 1])) {
      if ( 207 == $response['statusCode']) {
        if ( $multi = $this->_client->parseMultiStatus($response['body'])) {
          foreach ($multi as $_k => $_v) {
            $_e = $_v[200];
            // \sys::dump( $_e, 'Event', false);

            $events[] = (object)[
              'calendar' => $calendar->name,
              'path' => $_k,
              'uid' => str_replace( $calendar->path, '', $_k),
              'etag' => trim( $_e['{DAV:}getetag'], '"' ),
              'data' => $_e[$key],

            ];

          }

        }

      }

    }

    if ( $debug) {
      \sys::logger(
        sprintf(
          '<%s event/s> <%s> %s',
          count( $events),
          \application::timer()->elapsed(),
          __METHOD__

        )

      );

    }

    return $events;

  }

  public function createEvent( object $calendar, Sabre\VObject\Component\VCalendar $vcalendar) : response {

    $url = sprintf( '%s%s.ics', $calendar->path, $vcalendar->VEVENT->UID);
    if ( $response = $this->_client->request('PUT', $url, $vcalendar->serialize())) {
      if ( '201' == $response['statusCode']) {
        if ( isset( $response['headers']['etag'])) {
          $ret = new response;
          $ret->Id = $response['headers']['etag'][0];
          $ret->ResponseType = 'CalendarItem';

        }

      }

      return $ret;

    }

    return null;

  }

}
