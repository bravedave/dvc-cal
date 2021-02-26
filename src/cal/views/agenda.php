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

use currentUser;
use DateTime;
use DateInterval;
use strings;

$seed = new DateTime( $this->data->seed);

$haveCalendar = (bool)currentUser::getCalendarCredentials();

for ($i=0; $i < 7; $i++) {
  if ( $i > 0) $seed->add( new DateInterval('P1D'));  ?>
  <div data-date="<?= $seed->format( 'Y-m-d') ?>">
    <div class="form-row mb-1">
      <div class="col bg-light py-2">
        <div class="d-flex">
          <h5 class="flex-fill m-0 pt-2"><?= $seed->format( 'D M j') ?></h5>
        <?php if ( $haveCalendar) { ?>
          <button type="button" class="btn btn-light" data-role="btn-calendar-add" data-date="<?= $seed->format( 'Y-m-d') ?>"><i class="bi bi-calendar-plus"></i></button>

        <?php } // if ( $haveCalendar) { ?>

        </div>

      </div>

    </div>

  </div>

<?php
}

if ( $haveCalendar) { ?>
<script>
( _ => {
  $('button[data-role="btn-calendar-add"]').each( (i, btn) => {
    $(btn).on( 'click', function( e) {
      e.stopPropagation();e.preventDefault();

      let _me = $(this);
      let _data = _me.data();

      _.get.modal( _.url( '<?= $this->route ?>/appointment'))
      .then( modal => {
        let d = _.dayjs( _data.date);
        $('input[name="date"]', modal).val( d.format( 'YYYY-MM-DD'));

        modal.on( 'success', e => $(document).trigger('load-active-feeds'));

        // console.log( _data);

      });

    });

  });

}) (_brayworth_);
</script>

<?php
} // if ( $haveCalendar) { ?>
