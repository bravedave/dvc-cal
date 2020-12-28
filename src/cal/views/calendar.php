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

use strings;

$_accordion = strings::rand();  ?>

<nav>
  <div class="nav nav-tabs" role="tablist" id="<?= $_accordion ?>-tablist">
    <div class="nav-item">
      <div class="input-group">
        <input type="date" class="form-control" id="<?= $_accordion ?>-date"
          value="<?= date( 'Y-m-d') ?>">

        <div class="input-group-append">
          <button type="button" class="btn input-group-text" id="<?= $_accordion ?>-date-refresh"><i class="bi bi-arrow-repeat"></i></button>

        </div>

      </div>

    </div>

    <a class="nav-link small ml-auto" data-toggle="tab" href="#<?= $_accordion ?>-agenda-tab" id="<?= $_accordion ?>-agenda" aria-selected="true" aria-controls="<?= $_accordion ?>-agenda-tab">
      Agenda
    </a>

    <a class="nav-link small" data-toggle="tab" href="#<?= $_accordion ?>-week-tab" id="<?= $_accordion ?>-week" aria-selected="false" aria-controls="<?= $_accordion ?>-week-tab">
      Week
    </a>

    <a class="nav-link small" data-toggle="tab" href="#<?= $_accordion ?>-month-tab" id="<?= $_accordion ?>-month" aria-selected="false" aria-controls="<?= $_accordion ?>-month-tab">
      Month
    </a>

  </div>

</nav>

<div class="tab-content">
  <div id="<?= $_accordion ?>-agenda-tab" class="tab-pane fade" role="tabpanel" aria-labeled-by="#<?= $_accordion ?>-agenda"></div>
  <div id="<?= $_accordion ?>-week-tab" class="tab-pane fade" role="tabpanel" aria-labeled-by="#<?= $_accordion ?>-week"></div>
  <div id="<?= $_accordion ?>-month-tab" class="tab-pane fade" role="tabpanel" aria-labeled-by="#<?= $_accordion ?>-month"></div>

</div>
<script>
( _ => {
  let getFeed = (feed, tab) => {
    return new Promise( resolve => {
      if ( /post/i.test( String( feed.method))) {
        let date = _.dayjs($('#<?= $_accordion ?>-date').val());
        let edate = date.add('7', 'days');

        _.post({
          url : feed.url,
          data : {
            action : 'get-feed',
            name : feed.name,
            start : date.format( 'YYYY-MM-DD'),
            end : edate.format( 'YYYY-MM-DD'),

          },

        }).then( d => {
          if ( 'ack' == d.response) {
            $.each( d.data, ( i, event) => {
              tab.trigger( 'event-add', event)
              // console.log( event);

            });

          }

          resolve();

        });

      }

    });

  };

  $('#<?= $_accordion ?>-agenda')
  .on( 'event-add', function(e, event) {
    let tab = $('#<?= $_accordion ?>-agenda-tab');
    let date = _.dayjs( event.start);
    let edate = _.dayjs( event.end);
    let key = 'div[data-date="' + date.format('YYYY-MM-DD') + '"]';
    let container = $(key, tab);

    let row = $('<div class="form-row mb-2"></div>');
    $('<div class="col-5 col-md-4 col-xl-3 text-truncate"></div>')
    .html( date.format( 'h:mm a') + ' - ' + edate.format( 'h:mm a'))
    .appendTo( row);

    $('<div class="col-7 col-md-8 col-xl-7"></div>')
    .html( event.summary)
    .appendTo( row);

    row.appendTo( container);

  })
  .on( 'update-tab', function(e) {
    let _me = $(this);
    let tab = $('#<?= $_accordion ?>-agenda-tab');
    let date = _.dayjs($('#<?= $_accordion ?>-date').val());
    let url = '<?= $this->route ?>/agenda?seed=' + date.format( 'YYYY-MM-DD');

    tab.load( url, html => {
      let feeds = $(document).data('active_feeds');
      let i = 0;

      let getNextFeed = () => {
        if ( feeds.length > i) {
          getFeed( feeds[i++], _me)
          .then( getNextFeed);

        }

      };

      console.table( 'update-tab-agenda');
      getNextFeed();

    });

  })
  .on( 'show.bs.tab', function(e) {
    $(this).trigger( 'update-tab');

  })
  .on( 'hidden.bs.tab', function(e) {
    let tab = $('#<?= $_accordion ?>-agenda-tab');
    tab.html('');

  });

  $('#<?= $_accordion ?>-week')
  .on( 'event-add', function(e, event) {
    // console.log( 'event-add-week');
    let tab = $('#<?= $_accordion ?>-week-tab');
    let date = _.dayjs( event.start);
    let edate = _.dayjs( event.end);
    let key = 'div[data-date="' + date.format('YYYY-MM-DD') + '"]';
    let container = $(key, tab);

    let row = $('<div class="form-row mb-2"></div>');
    $('<div class="col-5 text-truncate"></div>')
    .html( date.format( 'h:mm a'))
    .appendTo( row);

    $('<div class="col-7"></div>')
    .html( event.summary)
    .appendTo( row);

    row.appendTo( container);

  })
  .on( 'update-tab', function(e) {
    let _me = $(this);
    let tab = $('#<?= $_accordion ?>-week-tab');
    let date = _.dayjs($('#<?= $_accordion ?>-date').val());
    let url = '<?= $this->route ?>/week?seed=' + date.format( 'YYYY-MM-DD');

    tab.load( url, html => {
      let feeds = $(document).data('active_feeds');
      let i = 0;

      let getNextFeed = () => {
        if ( feeds.length > i) {
          getFeed( feeds[i++], _me)
          .then( getNextFeed);

        }

      };

      console.table( 'update-tab-week');
      getNextFeed();

    });

  })
  .on( 'show.bs.tab', function(e) {
    $(this).trigger( 'update-tab');

  })
  .on( 'hidden.bs.tab', function(e) {
    let tab = $('#<?= $_accordion ?>-agenda-week');
    tab.html('');

  });

  $('#<?= $_accordion ?>-month')
  .on( 'update-tab', function(e) {
    // console.log( 'update-tab-month');
    let feeds = $(document).data('active_feeds');
    console.table( feeds);

  })
  .on( 'show.bs.tab', function(e) {
    $(this).trigger( 'update-tab');

  })
  .on( 'hidden.bs.tab', function(e) {
    let tab = $('#<?= $_accordion ?>-agenda-month');
    tab.html('');

  });

  $('#<?= $_accordion ?>-tablist').on( 'update-active-tab', function( e) {
    let activeTab = $('a.active', this);
    if ( activeTab.length > 0) {
      activeTab.trigger('update-tab');

    }
    else {
      $('#<?= $_accordion ?>-agenda').tab('show');

    }

  });

  $('#<?= $_accordion ?>-date-refresh').on( 'click', function( e) {
    e.stopPropagation();
    $('#<?= $_accordion ?>-tablist').trigger( 'update-active-tab');

  })

  $(document).on('load-active-feeds', (e) => {
    _.post({
      url : _.url('<?= $this->route ?>'),
      data : {
        action : 'get-active-feeds'

      },

    }).then( d => {
      if ( 'ack' == d.response) {
        $(document).data('active_feeds', d.data);
        $('#<?= $_accordion ?>-tablist').trigger( 'update-active-tab');

      }
      else {
        _.growl( d);

      }

    });

  });

  $(document).ready( () => $(document).trigger('load-active-feeds'));

}) (_brayworth_);
</script>