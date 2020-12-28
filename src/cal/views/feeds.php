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
use strings;  ?>

<div class="nav flex-column" id="<?= $_feedlist = strings::rand() ?>">
  <div class="nav-item h5">Feeds</div>
  <?php
  $feeds = config::dvc_cal_feeds();
  foreach ($feeds as $feed) { ?>
    <div class="nav-item">
      <div class="nav-link">
        <div class="form-check">
          <input type="checkbox" class="form-check-input"
            data-name="<?= $feed->name ?>"
            <?php if ( 'yes' == currentUser::option( 'cal-feed-' . $feed->name)) print 'checked' ?>
            id="<?= $uid = strings::rand() ?>">

          <label class="form-check-label" for="<?= $uid ?>">
            <?= $feed->name ?>

          </label>

        </div>

      </div>

    </div>

  <?php
  } ?>

</div>
<script>
( _ => {
  $('input[type="checkbox"]', '#<?= $_feedlist ?>').each( (i, feed) => {
    $(feed).on( 'change', function(e) {
      let _me = $(this);
      let _data = _me.data();

      _.post({
        url : _.url('<?= $this->route ?>'),
        data : {
          action : 'toggle-feed',
          feed : _data.name,
          state : _me.prop( 'checked') ? 'yes' : 'no'

        },

      }).then( d => {
        _.growl( d);
        $(document).trigger('load-active-feeds');

      });

    });

  });

}) (_brayworth_);
</script>