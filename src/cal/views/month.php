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

use DateTime;
use DateInterval;
use strings;

$seed = new DateTime( $this->data->seed);
$month = $seed->format('n');
?>

<div class="form-row">
  <div class="col text-center h6 my-2"><?= $seed->format('F') ?></div>

</div>
<div class="form-row border-bottom">
  <div class="col text-center small">Sun</div>
  <div class="col text-center small">Mon</div>
  <div class="col text-center small">Tue</div>
  <div class="col text-center small">Wed</div>
  <div class="col text-center small">Thu</div>
  <div class="col text-center small">Fri</div>
  <div class="col text-center small">Sat</div>

</div>
<?php
$day = 0;
$hardWidth = ((int)(100 / 7 * 100))/100;
print '<div class="form-row mb-1">';
while ( $day < $seed->format('w')) {
  $day ++;
  printf( '<div class="col" style="width: %s%%;">&nbsp;</div>', $hardWidth);

}
while ( $month == $seed->format('n')) {

  printf(
    '<div class="col py-2" style="min-height: 6rem; width: %s%%;" data-date="%s"><div class="form-row"><div class="col bg-light">%s<sup>%s</sup></div></div></div>',
    $hardWidth,
    $seed->format( 'Y-m-d'),
    $seed->format( 'j'),
    $seed->format( 'S')

  );

  if ( ++$day == 7) {
    $day = 0;
    print '</div><div class="form-row mb-1">';

  }

  $seed->add( new DateInterval('P1D'));

};
while ( $day++ < 6) {
  print '<div class="col">&nbsp;</div>';

}
print '</div>';
