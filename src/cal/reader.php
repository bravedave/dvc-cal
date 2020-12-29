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
class reader {

  protected $_feed = [];

  protected function __construct() {}

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
        'description' => $description,
        'data' => $event->printData()

      ]);

    }

    return $reader;

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

      $reader->append([
        'summary' => $event->summary,
        'start' => date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtstart)),
        'end' => date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtend)),
        'startUTC' => date('c', $ical->iCalDateToUnixTimestamp($event->dtstart)),
        'endUTC' => date('c', $ical->iCalDateToUnixTimestamp($event->dtend)),
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

  public function feed( $start, $end) : array {
    $a = [];
    foreach ( $this->_feed as $event) {
      $es = date( 'Y-m-d', strtotime( $event['start']));
      if ( $es >= $start && $es <= $end) {
        $a[] = $event;

      }

    }

    return $a;

  }

}
