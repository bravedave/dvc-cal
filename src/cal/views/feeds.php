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
  foreach ($this->feeds as $feed) {
    $active = 'yes' == currentUser::option( 'cal-feed-' . $feed->name);
    ?>
    <div class="nav-item">
      <a class="nav-link" data-name="<?= $feed->name ?>" data-active="<?= $active ? 'yes' : 'no' ?>" href="#">
        <?php
          printf(
            '<i class="bi %s" style="color: %s"></i>',
            $active ? 'bi-check-square-fill' : 'bi-square',
            $feed->color

          );

        ?>

        <?= $feed->name ?>

      </a>

    </div>

  <?php
  } ?>

</div>
<script>
( _ => {
  $('a.nav-link[data-name]', '#<?= $_feedlist ?>').each( (i, feed) => {
    $(feed)
    .on( 'click', function( e) {
      e.stopPropagation();e.preventDefault();

      let _me = $(this);
      let _data = _me.data();
      let newState = 'yes' == _data.active ? 'no' : 'yes';

      _.post({
        url : _.url('<?= $this->route ?>'),
        data : {
          action : 'toggle-feed',
          feed : _data.name,
          state : newState

        },

      }).then( d => {
        _.growl( d);

        _me.data('active', newState);

        $('.bi', _me)
        .removeClass()
        .addClass( 'yes' == newState ? 'bi bi-check-square-fill' : 'bi bi-square');

        $(document).trigger('load-active-feeds');

      });

    });

  });

}) (_brayworth_);
</script>