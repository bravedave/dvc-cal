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

use ICal\ICal;
use DateTime;
use DateInterval;
use ParseCsv;

class reader {

  protected $_feed = [];

  protected function __construct() {}

  static public function CSVString( string $input) : self {
    $csv = new ParseCsv\Csv;
    $csv->parse( $input);

    $reader = new self;
    foreach ( $csv->data as $event) {
      /* [0] => Array
        (
            [Date] => 20210101
            [Holiday Name] => New Year's Day
            [Information] => New Year's Day is the first day of the calendar year and is celebrated each January 1st
            [More Information] => https://www.cmtedd.act.gov.au/communication/holidays
            [Jurisdiction] => act
        ) */

      // \sys::dump( $event);

      $date = new DateTime( $event['Date'], new \DateTimeZone( config::$TIMEZONE));
      $end = new DateTime( $event['Date'], new \DateTimeZone( config::$TIMEZONE));
      $end->add( new DateInterval('P1D'));

      $id = sprintf('%s-%s@pub', $event['Date'], preg_replace( '@[^a-z0-9]@i', '-', $event['Holiday Name']));

      $reader->append([
        'summary' => $event['Holiday Name'],
        'start' => $date->format('Y-m-d H:i'),
        'end' => $end->format('Y-m-d H:i'),
        'startUTC' => $date->format('c'),
        'endUTC' => $end->format('c'),
        'location' => $event['Jurisdiction'],
        'id' => $id

      ]);

    }

    return $reader;

  }

  static public function ICSString( string $string) : self {
    $ical = new ICal( false, [
        'defaultSpan'                 => 2,     // Default value
        'defaultTimeZone'             => 'UTC',
        'defaultWeekStart'            => 'MO',  // Default value
        'disableCharacterReplacement' => false, // Default value
        'filterDaysAfter'             => null,  // Default value
        'filterDaysBefore'            => null,  // Default value
        'skipRecurrence'              => false, // Default value
    ]);
    $ical->initString( $string);

    $reader = new self;

    foreach ( $ical->events() as $event) {
      $description = str_replace( '\n', PHP_EOL, $event->description);
      $description = str_replace( '\,', ',', $description);

      $reader->append([
        'summary' => $event->summary,
        'start' => date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtstart)),
        'end' => date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtend)),
        'startUTC' => date('c', $ical->iCalDateToUnixTimestamp($event->dtstart)),
        'endUTC' => date('c', $ical->iCalDateToUnixTimestamp($event->dtend)),
        'location' => $event->location,
        'uid' => $event->uid,
        'description' => $description,
        'data' => $event->printData()

      ]);

    }

    return $reader;

  }

  static public function readCSV( string $path) : self {
    return self::CSVString( \file_get_contents( $path));

  }

  static public function readICS( string $path) : self {
    $ical = new ICal( $path, [
      'defaultSpan'                 => 2,     // Default value
      'defaultTimeZone'             => 'UTC',
      'defaultWeekStart'            => 'MO',  // Default value
      'disableCharacterReplacement' => false, // Default value
      'filterDaysAfter'             => null,  // Default value
      'filterDaysBefore'            => null,  // Default value
      'skipRecurrence'              => false, // Default value
    ]);
    // $ical->initFile('ICal.ics');
    // $ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics', $username = null, $password = null, $userAgent = null);

    $reader = new self;
    foreach ( $ical->events() as $event) {
      $description = str_replace( '\n', PHP_EOL, $event->description);
      $description = str_replace( '\,', ',', $description);

      $start = new DateTime(($event->dtstart));
      $start->setTimezone( new \DateTimeZone( config::$TIMEZONE));
      $end = new DateTime(($event->dtend));
      $end->setTimezone( new \DateTimeZone( config::$TIMEZONE));

      $reader->append([
        'summary' => $event->summary,
        'start' => $start->format('Y-m-d H:i'),
        'end' => $end->format('Y-m-d H:i'),
        'startUTC' => $start->format('c'),
        'endUTC' => $end->format('c'),
        'location' => $event->location,
        'description' => $description,
        'data' => $event->printData()

      ]);

    }

    return $reader;

  }

  static public function readJSON( string $path) : self {
    return self::JSONString( \file_get_contents( $path));

  }

  static public function JSONString( string $json) : self {
    $json = json_decode( $json);
    $reader = new self;

    foreach ( $json as $event) {
      /* {
        "title":"Daily Meeting Team D'Arcy",
        "location":"",
        "notes":"",
        "start":"2020-12-02T08:30:00+10:00",
        "end":"2020-12-02T08:45:00+10:00",
        "id":"AAMkADY4OGM5MWMwLTVhY2ItNGFkZi1hMmFkLTY4OTllN2M2YzEzNgFRAAgI2JZVNkgAAEYAAAAAIf0Q0BV1j0KWt+MMJasV4wcAea7WiVlW9kei4Ra+phJGNQAAAOK4RAAAea7WiVlW9kei4Ra+phJGNQAAOEhUpgAAEA==",
        "allDay":false,
        "changekey":"DwAAABYAAAB5rtaJWVb2R6LhFr6mEkY1AAA4d8DN"
      } */

      $start = new DateTime( $event->start);
      $end = new DateTime( $event->end);

      $reader->append([
        'summary' => $event->title,
        'start' => $start->format('Y-m-d H:i'),
        'end' => $end->format('Y-m-d H:i'),
        'startUTC' => $start->format('c'),
        'endUTC' => $end->format('c'),
        'location' => $event->location,
        'description' => $event->notes,
        'id' => $event->id

      ]);

    }

    return $reader;

  }

  public function append( array $a ) {
    $this->_feed[] = $a;

  }

  public function asJson() : string {
    return json_encode( $this->_feed);

  }

  public function feed( string $start = '', string $end = '', $filter = false) : array {

    if ( $start || $end || $filter) {
      $a = [];
      foreach ( $this->_feed as $event) {
        $es = date( 'Y-m-d', strtotime( $event['start']));
        if ( !$start || $es >= $start) {
          if ( !$end || $es <= $end) {
            if ( $filter) {
              if ( $filter( $event)) {
                $a[] = $event;

              }

            }
            else {
              $a[] = $event;

            }

          }

        }

      }

      return $a;

    }

    return $this->_feed;

  }

}
