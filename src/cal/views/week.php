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


$slots = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];  ?>

<div class="form-row mb-1">
  <div class="col-1">
    <div class="row">
      <div class="col d-flex" ctrl-box></div>
    </div>

  </div>

  <?php
  $seed = new DateTime($this->data->seed);
  for ($i = 0; $i < 7; $i++) {
    if ($i > 0) $seed->add(new DateInterval('P1D'));  ?>

    <div class="col pt-2" data-date="<?= $seed->format('Y-m-d') ?>">
      <div class="d-flex">
        <h6 class="flex-fill mt-2 mb-0"><?= $seed->format('D j') ?><sup><?= $seed->format('S') ?></sup></h6>
        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
          <div class="btn-group btn-group-sm" role="group" aria-label="small button group"
            id="<?= $_uid = strings::rand() ?>">
          </div>
        </div>
        <script>
          $(document).trigger('calendar-toolbar-created', '#<?= $_uid ?>');
        </script>
      </div>
    </div>
  <?php
  } ?>

</div>

<div class="form-row border-bottom d-none" style="min-height: 4rem;" day-slot>
  <div class="col"></div>

  <?php
  $seed = new DateTime($this->data->seed);
  for ($i = 0; $i < 7; $i++) {
    if ($i > 0) $seed->add(new DateInterval('P1D'));  ?>

    <div class="col py-2" style="width: 13.09%" data-date="<?= $seed->format('Y-m-d') ?>" data-slot="day">
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

    $seed = new DateTime($this->data->seed);
    for ($i = 0; $i < 7; $i++) {
      if ($i > 0) $seed->add(new DateInterval('P1D'));  ?>

      <div class="col py-2" style="width: 13.09%" data-date="<?= $seed->format('Y-m-d') ?>"
        data-slot="<?= $slot ?>"></div>

    <?php
    } ?>

  </div>

<?php
} ?>