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

use currentUser;
use DateTime;
use DateInterval;
use strings;

$seed = new DateTime($this->data->seed);

$haveCalendar = (bool)currentUser::getCalendarCredentials();

for ($i = 0; $i < $this->data->days; $i++) {

  if ($i > 0) $seed->add(new DateInterval('P1D'));  ?>

  <div data-date="<?= $seed->format('Y-m-d') ?>">

    <div class="row g-2 mb-1">

      <div class="col bg-light py-2">

        <div class="d-flex">

          <h5 class="flex-fill m-0 pt-2"><?= $seed->format('D M j') ?></h5>
          <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups"
            id="<?= $_uid = strings::rand() ?>">
            <?php if ($haveCalendar) { ?>

              <button type="button" class="btn btn-light" data-role="btn-calendar-add"
                data-date="<?= $seed->format('Y-m-d') ?>">
                <i class="bi bi-calendar-plus"></i></button>
            <?php } // if ( $haveCalendar) {
            ?>
          </div>
          <script>
            $(document).trigger('calendar-toolbar-created', '#<?= $_uid ?>');
          </script>
        </div>
      </div>
    </div>
  </div>
<?php
}

if ($haveCalendar) { ?>
  <script>
    (_ => {

      $('button[data-role="btn-calendar-add"]').each((i, btn) => {

        $(btn).on('click', function(e) {

          e.stopPropagation();
          e.preventDefault();

          const _me = $(this);
          const _data = _me.data();

          _.get.modal(_.url('<?= $this->route ?>/appointment'))
            .then(modal => {

              const d = _.dayjs(_data.date);
              $('input[name="date"]', modal).val(d.format('YYYY-MM-DD'));

              modal.on('success', e => $(document)
                .trigger('load-active-feeds'));
            });
        });
      });
    })(_brayworth_);
  </script>
<?php
}
