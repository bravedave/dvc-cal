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

use Sabre;

class client {
  protected $_client;

  protected $_calendarRoot;

  protected $_calendars = [];

  public $principal;

  function __construct( array $settings) {
    $this->_client = new Sabre\DAV\Client($settings);

    /**
     * Get a path for the user’s principal resource on the server.
     */
    if ( $props = $this->_client->propfind('', [ '{DAV:}current-user-principal' ], $depth = 0)) {
      $this->principal = $props['{DAV:}current-user-principal'][0]['value'];

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

            }

          }

        }

      }

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
                    // else {
                    //   \sys::dump( $first[200]);

                    // }

                  }

                }

              }

              $this->_calendars[ $_cal->name] = $_cal;

            }

          }

        }

      }

    }

    return $this->_calendars;

  }

  public function getEvents( object $calendar, $from, $to) : array {
    // printf(
    //   '<br>time-range start="%sT000000" end="%sT000000"',
    //   date( 'Ymd', strtotime( 'last monday')),
    //   date( 'Ymd', strtotime( '+2 day')),

    // );
    $body = implode( '', [
      '<?xml version="1.0"?>',
      '<c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">',
        '<d:prop><d:getetag /><c:calendar-data /></d:prop>',
        '<c:filter>',
          '<c:comp-filter name="VCALENDAR">',
            '<c:comp-filter name="VEVENT">',
              sprintf(
                '<c:time-range  start="%sT000000" end="%sT000000"/>',
                date( 'Ymd', strtotime( $from)),
                date( 'Ymd', strtotime( $to))

              ),
            '</c:comp-filter>',
          '</c:comp-filter>',
        '</c:filter>',
      '</c:calendar-query>'

    ]);

    /*
    $body = implode( '', [
      '<?xml version="1.0"?>',
      '<c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">',
        '<d:prop><d:getetag /><c:calendar-data /></d:prop>',
        '<c:filter>',
          '<c:comp-filter name="VCALENDAR" />',
        '</c:filter>',
      '</c:calendar-query>'

    ]);
    */

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
              'etag' => $_e['{DAV:}getetag'],
              'data' => $_e[$key],

            ];

          }

        }

      }

    }

    return $events;

  }

}
