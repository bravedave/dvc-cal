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

for ($i=0; $i < 7; $i++) {
  if ( $i > 0) $seed->add( new DateInterval('P1D'));  ?>
  <div data-date="<?= $seed->format( 'Y-m-d') ?>">
    <div class="form-row mb-1">
      <div class="col bg-light py-2">
        <h5 class="m-0"><?= $seed->format( 'D, M j') ?></h5>

      </div>

    </div>

  </div>

<?php
} ?>
