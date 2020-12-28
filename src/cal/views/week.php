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

$seed = new DateTime( $this->data->seed); ?>

<div class="form-row mb-1">
  <?php
  for ($i=0; $i < 7; $i++) {
    if ( $i > 0) $seed->add( new DateInterval('P1D'));  ?>

    <div class="col bg-light py-2">
      <h6 class="m-0 text-center"><?= $seed->format( 'D jS') ?></h5>
      <div data-date="<?= $seed->format( 'Y-m-d') ?>"></div>

    </div>

  <?php
  } ?>

</div>
