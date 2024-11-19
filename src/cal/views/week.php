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
use strings;

extract((array)$this->data);
/** @var string $seed */
$slots = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];  ?>

<div class="row g-2 mb-1">
  <div class="col-1">

    <div class="row">
      <div class="col d-flex" ctrl-box></div>
    </div>
  </div>

  <?php
  $ds = new DateTime($seed);
  for ($i = 0; $i < 7; $i++) {
    if ($i > 0) $ds->add(new DateInterval('P1D'));  ?>

    <div class="col" data-date="<?= $ds->format('Y-m-d') ?>">
      <div class="d-flex">
        <h6 class="flex-fill mt-2 mb-0"><?= $ds->format('D j') ?><sup><?= $ds->format('S') ?></sup></h6>
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

<div class="row g-2 border-bottom d-none" style="min-height: 4rem;" day-slot>

  <div class="col-1"></div>

  <?php
  $ds = new DateTime($seed);
  for ($i = 0; $i < 7; $i++) {
    if ($i > 0) $ds->add(new DateInterval('P1D'));

    printf(
      '<div class="col py-2" style="width: 13.09%%" data-date="%s" data-slot="day"></div>',
      $ds->format('Y-m-d')
    );
  } ?>
</div>

<?php
foreach ($slots as $slot) { ?>

  <div class="row g-2 border-bottom" style="min-height: 4rem;">

    <?php
    printf(
      '<div class="col-1 text-center">%s%s</div>',
      $slot > 12 ? $slot - 12 : $slot,
      $slot > 12 ? 'p' : 'a'

    );

    $ds = new DateTime($seed);
    for ($i = 0; $i < 7; $i++) {
      if ($i > 0) $ds->add(new DateInterval('P1D'));

      printf(
        '<div class="col py-2" style="width: 13.09%%" data-date="%s" data-slot="%s"></div>',
        $ds->format('Y-m-d'),
        $slot
      );
    } ?>
  </div>
<?php
}
