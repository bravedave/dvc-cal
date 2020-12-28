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

class reader {

  protected $_feed = [];

  protected function __construct() {}

  static public function readICS( $path) : self {
    try {
        $ical = new ICal( $path, array(
            'defaultSpan'                 => 2,     // Default value
            'defaultTimeZone'             => 'UTC',
            'defaultWeekStart'            => 'MO',  // Default value
            'disableCharacterReplacement' => false, // Default value
            'filterDaysAfter'             => null,  // Default value
            'filterDaysBefore'            => null,  // Default value
            'skipRecurrence'              => false, // Default value
        ));
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

    } catch (\Exception $e) {
        die($e);

    }

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
