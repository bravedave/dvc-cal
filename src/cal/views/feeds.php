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
use strings;

extract((array)$this->data); ?>

<div class="nav flex-column" id="<?= $_feedlist = strings::rand() ?>">

  <div class="nav-item h5">Feeds</div>
  <?php
  foreach ($this->feeds as $feed) {

    if ('---' == $feed->name) {

      print '<div class="nav-item"><hr class="my-1"></div>';
    } else {

      $active = 'yes' == currentUser::option('cal-feed-' . $feed->name); ?>

      <div class="nav-item">

        <a class="nav-link" href="#"
          data-name="<?= $feed->name ?>"
          data-active="<?= $active ? 'yes' : 'no' ?>">

          <?php
          $color = $feed->color;
          $backgroundColor = '#000';
          if (isset($feed->forecolor) && $feed->forecolor) {

            $backgroundColor = $feed->forecolor;
          }

          // effectively color is reversed
          printf(
            '<i class="bi d-inline-flex %s" style="color: %s; background-color: %s;"></i>',
            $active ? 'bi-check-square-fill' : 'bi-square-fill',
            $color,
            $backgroundColor
          );

          if (isset($feed->personal) && $feed->personal) {

            printf(' <strong>%s</strong>', $feed->name);
          } else {

            printf(' <span>%s</span>', $feed->label ?? $feed->name);
          } ?>
        </a>
      </div>
  <?php
    }
  } ?>

</div>
<script>
  (_ => {
    const feedlist = $('#<?= $_feedlist ?>');

    feedlist.find('a.nav-link[data-name]').each((i, feed) => {

      $(feed).on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();

        const _me = $(this);
        const _data = _me.data();
        const newState = 'yes' == _data.active ? 'no' : 'yes';

        _.fetch.post(_.url('<?= $this->route ?>'), {
          action: 'toggle-feed',
          feed: _data.name,
          state: newState
        }).then(d => {

          _.growl(d);
          _me.data('active', newState);

          $('.bi', _me)
            .removeClass()
            .addClass('bi d-inline-flex')
            .addClass('yes' == newState ?
              'bi-check-square-fill' :
              'bi-square-fill');

          $(document).trigger('load-active-feeds');
        });
      });
    });
  })(_brayworth_);
</script>