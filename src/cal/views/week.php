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


$slots = [6,7,8,9,10,11,12,13,14,15,16,17,18];  ?>

<div class="form-row mb-1">
  <div class="col-1">
    <div class="row"><div class="col d-flex" ctrl-box></div></div>

  </div>

  <?php
  $seed = new DateTime( $this->data->seed);
  for ($i=0; $i < 7; $i++) {
    if ( $i > 0) $seed->add( new DateInterval('P1D'));  ?>

    <div class="col py-2 text-center">
      <strong><?= $seed->format( 'D jS') ?></strong>

    </div>

  <?php
  } ?>

</div>

<?php
foreach ($slots as $slot) { ?>
  <div class="form-row border-bottom" style="min-height: 4rem;">

  <?php
  printf(
    '<div class="col-1 text-center">%s%s</div>',
    $slot > 12 ? $slot - 12 : $slot,
    $slot > 12 ? 'p' : 'a'

  );

  $seed = new DateTime( $this->data->seed);
  for ($i=0; $i < 7; $i++) {
    if ( $i > 0) $seed->add( new DateInterval('P1D'));  ?>

    <div class="col py-2" style="width: 13.09%" data-date="<?= $seed->format( 'Y-m-d') ?>" data-slot="<?= $slot ?>"></div>

  <?php
  } ?>

  </div>

<?php
} ?>
