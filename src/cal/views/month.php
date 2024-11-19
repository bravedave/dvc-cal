<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * replace:
 * [x] data-dismiss => data-bs-dismiss
 * [x] data-toggle => data-bs-toggle
 * [x] data-content => data-bs-content
 * [x] data-title => data-bs-title
 * [x] data-trigger => data-bs-trigger
 * [x] data-html => data-bs-html
 * [x] data-parent => data-bs-parent
 * [x] text-right => text-end
 * [x] custom-select - form-select
 * [x] mr-* => me-*
 * [x] ml-* => ms-*
 * [x] pr-* => pe-*
 * [x] pl-* => ps-*
 * [x] badge-pill rounded-pill
 * [x] badge-primary text-bg-primary
 * [x] badge-warning text-bg-warning
 * [x] font-weight-bold => fw-bold
 * [x] font-italic => fst-italic
 * [x] input-group-prepend - remove
 * [x] input-group-append - remove
 * [x] btn input-group-text => btn btn-light
 * [x] form-row => row g-2
 * [x] form-group => mb-2
 * [x] class="close" => class="btn-close"
 */

namespace dvc\cal;

use DateTime;
use DateInterval;

extract((array)$this->data);

$seed = new DateTime( $seed);
$month = $seed->format('n');
?>

<div class="row g-2">
  <div class="col my-2 d-flex" heading><h6 class="my-0 mx-auto p-1 text-center"><?= $seed->format('F') ?></h6></div>

</div>
<div class="row g-2 border-bottom">
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
print '<div class="row g-2 mb-1">';
while ( $day < $seed->format('w')) {
  $day ++;
  printf( '<div class="col" style="width: %s%%;">&nbsp;</div>', $hardWidth);

}
while ( $month == $seed->format('n')) {

  printf(
    '<div class="col py-2" style="min-height: 6rem; width: %s%%;" data-date="%s"><div class="row g-2"><div class="col bg-light d-flex" headline><div>%s<sup>%s</sup></div></div></div></div>',
    $hardWidth,
    $seed->format( 'Y-m-d'),
    $seed->format( 'j'),
    $seed->format( 'S')

  );

  if ( ++$day == 7) {
    $day = 0;
    print '</div><div class="row g-2 mb-1">';

  }

  $seed->add( new DateInterval('P1D'));

};
while ( $day++ < 7) {
  print '<div class="col">&nbsp;</div>';

}
print '</div>';
