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
<style>
  @media print {
    @page {
      size: landscape
    }
  }

  @media (max-width: 575.98px) {
    #<?= $_accordion ?>-date {
      -webkit-appearance: none;
    }
  }
</style>
<nav class="d-print-none d-none" id="<?= $_accordion ?>-nav">
  <div class="nav nav-tabs" role="tablist" id="<?= $_accordion ?>-tablist">
    <div class="nav-item">
      <div class="input-group">
        <div class="input-group-append">
          <button type="button" class="btn input-group-text" title="last week" id="<?= $_accordion ?>-date-last-monday"><i class="bi bi-chevron-left"></i></button>

        </div>

        <input type="date" class="form-control" autofocus id="<?= $_accordion ?>-date" value="<?= $this->data->start ?>">

        <div class="input-group-append">
          <button type="button" class="btn input-group-text" title="reload" id="<?= $_accordion ?>-date-refresh"><i class="bi bi-arrow-repeat"></i></button>

        </div>

        <div class="input-group-append">
          <button type="button" class="btn input-group-text" title="next week" id="<?= $_accordion ?>-date-next-monday"><i class="bi bi-chevron-right"></i></button>

        </div>

        <div class="input-group-append">
          <button type="button" class="btn input-group-text" title="in two weeks" id="<?= $_accordion ?>-date-next-monday-week"><i class="bi bi-chevron-double-right"></i></button>

        </div>

      </div>

    </div>

    <a class="nav-link d-none d-lg-block small ml-auto" data-toggle="tab" data-format="agenda" href="#<?= $_accordion ?>-agenda-tab" id="<?= $_accordion ?>-agenda" aria-selected="false" aria-controls="<?= $_accordion ?>-agenda-tab">
      Agenda
    </a>

    <a class="nav-link d-none d-lg-block small" data-toggle="tab" data-format="week" href="#<?= $_accordion ?>-week-tab" id="<?= $_accordion ?>-week" aria-selected="false" aria-controls="<?= $_accordion ?>-week-tab">
      Week
    </a>

    <a class="nav-link d-none d-lg-block small" data-toggle="tab" data-format="month" href="#<?= $_accordion ?>-month-tab" id="<?= $_accordion ?>-month" aria-selected="false" aria-controls="<?= $_accordion ?>-month-tab">
      Month
    </a>

    <a class="nav-link d-none small" data-toggle="tab" data-format="widget" href="#<?= $_accordion ?>-widget-tab" id="<?= $_accordion ?>-widget" aria-selected="false" aria-controls="<?= $_accordion ?>-widget-tab">
      Widget
    </a>

  </div>

</nav>

<div class="tab-content">
  <div id="<?= $_accordion ?>-agenda-tab" class="tab-pane fade" role="tabpanel" aria-labeled-by="#<?= $_accordion ?>-agenda"></div>
  <div id="<?= $_accordion ?>-week-tab" class="tab-pane fade small" role="tabpanel" aria-labeled-by="#<?= $_accordion ?>-week"></div>
  <div id="<?= $_accordion ?>-month-tab" class="tab-pane fade small" role="tabpanel" aria-labeled-by="#<?= $_accordion ?>-month"></div>
  <div id="<?= $_accordion ?>-widget-tab" class="tab-pane fade" role="tabpanel" aria-labeled-by="#<?= $_accordion ?>-widget"></div>

</div>
<script>
  (_ => {
    $('#<?= $_accordion ?>-date')
      .on('keypress', e => {
        if (13 == e.keyCode) {
          $('#<?= $_accordion ?>-tablist').trigger('update-active-tab');

        }

      });

    if (_.browser.isIPhone) {
      $('#<?= $_accordion ?>-date').on('change', function(e) {
        this.blur();
        $('#<?= $_accordion ?>-tablist').trigger('update-active-tab');

      });

    }

    let getFeed = (feed, tab) => {
      return new Promise(resolve => {
        if (/post/i.test(String(feed.method))) {
          let _data = tab.data();

          let date = _.dayjs($('#<?= $_accordion ?>-date').val());
          let edate = date.add('7', 'days');
          if ('week' == _data.format) {
            date = date.day(0);
            edate = date.add('7', 'days');

          } else if ('month' == _data.format) {
            date = date.date(1);
            edate = date.add('1', 'month').date(0);

          } else if ('widget' == _data.format) {
            date = date;
            edate = date.add('1', 'days');

          }

          let data = !!feed.data ? JSON.parse(feed.data) : {};
          data.action = 'get-feed';
          data.name = feed.name;
          data.start = date.format('YYYY-MM-DD');
          data.end = edate.format('YYYY-MM-DD');

          // console.table( data);

          // console.log( feed.name, feed.url);
          _.post({
            url: feed.url,
            data: data,

          }).then(d => {
            if ('ack' == d.response) {
              // console.log( d.data);
              $.each(d.data, (i, event) => tab.trigger('event-add', {
                event: event,
                feed: feed
              }));

            }

            resolve();

          });

        }

      });

    };

    let agendaRowMaker = (p, date, edate, allDay) => {

      let row = $('<div class="form-row border pointer-calendar" item></div>');
      row
        .data('data', p)
        .data('time', date.format('YYYY-MM-DD hh:mm'))
        .data('unix', date.unix())
        .data('allday', allDay ? 'yes' : 'no')
        .on('click', function(e) {
          e.stopPropagation();
          e.preventDefault();
          let _me = $(this);
          let _data = _me.data();
          _data.originalEvent = e;
          $(document).trigger('calendar-event-click', _data);

        })
        .on('contextmenu', function(e) {
          if (e.shiftKey)
            return;

          e.stopPropagation();
          e.preventDefault();
          let _me = $(this);
          let _data = _me.data();
          _data.originalEvent = e;
          $(document).trigger('calendar-event-context', _data);

        });

      let fmtStart = 0 == date.minute() ? date.format('h') : date.format('h:mm');
      let isEvent = date.unix() == edate.unix();
      if (isEvent) {
        fmtStart = 0 == date.minute() ? date.format('h a') : date.format('h:mm');

      }

      let fmtEnd = 0 == edate.minute() ? edate.format('h a') : edate.format('h:mm');
      let timeLabel = allDay ? 'all day' : (isEvent ? fmtStart : fmtStart + ' - ' + fmtEnd);
      $('<div class="col-3 col-xl-2 py-1 text-truncate"></div>')
        .html(timeLabel)
        .appendTo(row);

      $('<div class="col-auto small pt-1"><i class="bi bi-square-fill"></i></div>').css('color', p.feed.color).appendTo(row);

      $('<div class="col"></div>')
        .html(String(p.event.summary).replace(/loc:/, '<i class="bi bi-geo-alt-fill mr-1 small text-muted"></i>'))
        .appendTo(row);

      return row;

    };

    // window.goWidgit = e => $('#<?= $_accordion ?>-widget').tab('show');

    $('#<?= $_accordion ?>-widget')
      .on('event-add', function(e, p) {
        // console.log( p);

        let tab = $('#<?= $_accordion ?>-widget-tab');
        let date = _.dayjs(p.event.start);
        let edate = _.dayjs(p.event.end);
        let allDay = (date.unix() + 86400) == edate.unix() || date.format('YYYYMMDD') != edate.format('YYYYMMDD');

        let key = 'div[data-date="' + date.format('YYYY-MM-DD') + '"]';
        if (allDay) {
          let insertEvt = (p, date, edate, allDay, container) => {
            let items = $('> [item]', container);
            if (items.length > 0) {
              agendaRowMaker(p, date, edate, allDay).insertBefore(items[0]);

            } else {
              agendaRowMaker(p, date, edate, allDay).appendTo(container);

            }

          }

          insertEvt(p, date, edate, allDay, $(key, tab));
          if (date.format('YYYYMMDD') != edate.format('YYYYMMDD')) {
            for (let i = 1; i < 30; i++) {
              let _date = date.add(i, 'days');
              if (_date.format('YYYYMMDD') == edate.format('YYYYMMDD') && '0000' == edate.format('HHmm')) break;
              if (_date.format('YYYYMMDDHHmm') > edate.format('YYYYMMDDHHmm')) break;

              key = 'div[data-date="' + _date.format('YYYY-MM-DD') + '"]';
              insertEvt(p, date, edate, allDay, $(key, tab));

            }

          }

        } else {
          // insert at correct location
          let before = false;
          let container = $(key, tab);
          $('> [item]', container).each((i, row) => {
            let _row = $(row);
            let _data = _row.data();

            if ('yes' != _data.allDay) {
              if (date.unix() < _data.unix) {
                before = row;
                return false; // jQuery break

              }

            }

          });

          if (!!before) {
            agendaRowMaker(p, date, edate, allDay).insertBefore(before);

          } else {
            agendaRowMaker(p, date, edate, allDay).appendTo(container);

          }

        }

      })
      .on('update-tab', function(e) {
        let _me = $(this);
        let tab = $('#<?= $_accordion ?>-widget-tab');
        let date = _.dayjs($('#<?= $_accordion ?>-date').val());
        let url = _.url('<?= $this->route ?>/widget_guts?seed=' + date.format('YYYY-MM-DD'));

        tab.load(url, html => {
          let feeds = $(document).data('active_feeds');
          if (!!feeds) {
            let i = 0;

            $('.bi', '#<?= $_accordion ?>-date-refresh').addClass('bi-spin');

            let getNextFeed = () => {
              if (feeds.length > i) {
                getFeed(feeds[i++], _me)
                  .then(getNextFeed);

              } else {
                $('.bi', '#<?= $_accordion ?>-date-refresh').removeClass('bi-spin');

              }

            };

            getNextFeed();

          }

        });

      })
      .on('show.bs.tab', function(e) {
        $('#<?= $_accordion ?>-nav').addClass('d-none');
        $('#<?= $_accordion ?>-date-last-monday, #<?= $_accordion ?>-date-next-monday, #<?= $_accordion ?>-date-next-monday-week').addClass('d-none');
        $(this).trigger('update-tab');

      })
      .on('hidden.bs.tab', function(e) {
        let tab = $('#<?= $_accordion ?>-widget-tab');
        tab.html('');

      });

    $('#<?= $_accordion ?>-agenda')
      .on('event-add', function(e, p) {
        // console.log( p);

        let tab = $('#<?= $_accordion ?>-agenda-tab');
        let date = _.dayjs(p.event.start);
        let edate = _.dayjs(p.event.end);
        let allDay = (date.unix() + 86400) == edate.unix() || date.format('YYYYMMDD') != edate.format('YYYYMMDD');

        let key = 'div[data-date="' + date.format('YYYY-MM-DD') + '"]';
        if (allDay) {
          let insertEvt = (p, date, edate, allDay, container) => {
            let items = $('> [item]', container);
            if (items.length > 0) {
              agendaRowMaker(p, date, edate, allDay).insertBefore(items[0]);

            } else {
              agendaRowMaker(p, date, edate, allDay).appendTo(container);

            }

          }

          insertEvt(p, date, edate, allDay, $(key, tab));
          if (date.format('YYYYMMDD') != edate.format('YYYYMMDD')) {
            // console.log( date.format('YYYY-MM-DD HH:mm'), edate.format('YYYY-MM-DD HH:mm'));
            for (let i = 1; i < 30; i++) {
              let _date = date.add(i, 'days');
              if (_date.format('YYYYMMDD') == edate.format('YYYYMMDD') && '0000' == edate.format('HHmm')) break;
              if (_date.format('YYYYMMDDHHmm') > edate.format('YYYYMMDDHHmm')) break;

              key = 'div[data-date="' + _date.format('YYYY-MM-DD') + '"]';
              insertEvt(p, date, edate, allDay, $(key, tab));

            }

          }

        } else {
          // insert at correct location
          let before = false;
          let container = $(key, tab);
          $('> [item]', container).each((i, row) => {
            let _row = $(row);
            let _data = _row.data();

            if ('yes' != _data.allDay) {
              if (date.unix() < _data.unix) {
                before = row;
                return false; // jQuery break

              }

            }

          });

          if (!!before) {
            agendaRowMaker(p, date, edate, allDay).insertBefore(before);

          } else {
            agendaRowMaker(p, date, edate, allDay).appendTo(container);

          }

        }

      })
      .on('update-tab', function(e) {
        let _me = $(this);
        let tab = $('#<?= $_accordion ?>-agenda-tab');
        let date = _.dayjs($('#<?= $_accordion ?>-date').val());
        let url = '<?= $this->route ?>/agenda?seed=' + date.format('YYYY-MM-DD');

        tab.load(url, html => {
          let feeds = $(document).data('active_feeds');
          let i = 0;

          $('.bi', '#<?= $_accordion ?>-date-refresh').addClass('bi-spin');

          let getNextFeed = () => {
            if (feeds.length > i) {
              getFeed(feeds[i++], _me)
                .then(getNextFeed);

            } else {
              $('.bi', '#<?= $_accordion ?>-date-refresh').removeClass('bi-spin');

            }

          };

          getNextFeed();

        });

      })
      .on('show.bs.tab', function(e) {
        $('#<?= $_accordion ?>-nav').removeClass('d-none');
        $('#<?= $_accordion ?>-date-last-monday, #<?= $_accordion ?>-date-next-monday, #<?= $_accordion ?>-date-next-monday-week').removeClass('d-none');
        $(this).trigger('update-tab');

      })
      .on('hidden.bs.tab', function(e) {
        let tab = $('#<?= $_accordion ?>-agenda-tab');
        tab.html('');

      });

    $('#<?= $_accordion ?>-week')
      .on('event-add', function(e, p) {
        // console.log( 'event-add-week');
        let tab = $('#<?= $_accordion ?>-week-tab');
        let date = _.dayjs(p.event.start);
        // console.log( date.format('YYYY-MM-DD h:mm a'))
        let edate = _.dayjs(p.event.end);
        let allDay = (date.unix() + 86400) == edate.unix() || date.format('YYYYMMDD') != edate.format('YYYYMMDD');

        let rowMaker = (p, date, edate, allDay) => {
          let row = $('<div class="form-row pointer-calendar border" item></div>');

          row
            .attr('style', 'border-left: 3px solid ' + p.feed.color + '!important')
            .data('data', p)
            .data('time', date.format('YYYY-MM-DD hh:mm'))
            .data('unix', date.unix())
            .data('allday', allDay ? 'yes' : 'no')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              let _me = $(this);
              let _data = _me.data();
              _data.originalEvent = e;
              $(document).trigger('calendar-event-click', _data);

            })
            .on('contextmenu', function(e) {
              if (e.shiftKey)
                return;

              e.stopPropagation();
              e.preventDefault();
              let _me = $(this);
              let _data = _me.data();
              _data.originalEvent = e;
              $(document).trigger('calendar-event-context', _data);

            });

          $('<div class="col py-1 text-truncate"></div>')
            .attr('title', p.event.summary)
            .html(p.event.summary)
            .appendTo(row);

          return row;

        };

        // console.log( date.format('YYYY-MM-DD'), edate.format('YYYY-MM-DD'));
        if (allDay) {
          let key = 'div[data-date="' + date.format('YYYY-MM-DD') + '"][data-slot="day"]';
          $(key, tab).append(rowMaker(p, date, edate, allDay));

          // console.log( date.format('YYYY-MM-DD'), edate.format('YYYY-MM-DD'));
          if (date.format('YYYYMMDD') != edate.format('YYYYMMDD')) {
            for (let i = 1; i < 30; i++) {
              let _date = date.add(i, 'days');
              if (_date.format('YYYYMMDD') == edate.format('YYYYMMDD') && '0000' == edate.format('HHmm')) break;
              if (_date.format('YYYYMMDDHHmm') > edate.format('YYYYMMDDHHmm')) break;

              // console.log( _date.format('YYYY-MM-DD'));
              key = 'div[data-date="' + _date.format('YYYY-MM-DD') + '"][data-slot="day"]';
              $(key, tab).append(rowMaker(p, _date, edate, allDay));

            }

          }

          $('[day-slot]', tab).removeClass('d-none');

        } else {
          let key = 'div[data-date="' + date.format('YYYY-MM-DD') + '"][data-slot="' + date.format('H') + '"]';
          let container = $(key, tab);
          // insert at correct location
          let before = false;
          $('> [item]', container).each((i, row) => {
            let _row = $(row);
            let _data = _row.data();

            if ('yes' != _data.allDay) {
              if (date.unix() < _data.unix) {
                before = row;
                return false; // jQuery break

              }

            }

          });

          if (!!before) {
            rowMaker(p, date, edate, allDay).insertBefore(before);

          } else {
            rowMaker(p, date, edate, allDay).appendTo(container);

          }

        }


      })
      .on('update-tab', function(e) {
        let _me = $(this);
        let tab = $('#<?= $_accordion ?>-week-tab');
        let date = _.dayjs($('#<?= $_accordion ?>-date').val());
        let url = '<?= $this->route ?>/week?seed=' + date.format('YYYY-MM-DD');

        tab.load(url, html => {
          $('<button type="button" class="btn btn-light btn-sm flex-fill"><i class="bi bi-chevron-double-left"></i></button>')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();

              $('#<?= $_accordion ?>-date').val(date.add('-7', 'days').format('YYYY-MM-DD'));
              _me.trigger('update-tab');

            })
            .appendTo($('[ctrl-box]', tab))

          $('<button type="button" class="btn btn-light btn-sm flex-fill"><i class="bi bi-chevron-double-right"></i></button>')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();

              $('#<?= $_accordion ?>-date').val(date.add('7', 'days').format('YYYY-MM-DD'));
              _me.trigger('update-tab');

            })
            .appendTo($('[ctrl-box]', tab))

          $('.bi', '#<?= $_accordion ?>-date-refresh').addClass('bi-spin');

          let feeds = $(document).data('active_feeds');
          let i = 0;
          let getNextFeed = () => {
            if (feeds.length > i) {
              getFeed(feeds[i++], _me)
                .then(getNextFeed);

            } else {
              $('.bi', '#<?= $_accordion ?>-date-refresh').removeClass('bi-spin');

            }

          };

          // console.table( 'update-tab-week');
          getNextFeed();

        });

      })
      .on('show.bs.tab', function(e) {
        $('#<?= $_accordion ?>-nav').removeClass('d-none');
        $('#<?= $_accordion ?>-date-last-monday, #<?= $_accordion ?>-date-next-monday, #<?= $_accordion ?>-date-next-monday-week').addClass('d-none');
        $(this).trigger('update-tab');

      })
      .on('hidden.bs.tab', function(e) {
        let tab = $('#<?= $_accordion ?>-agenda-week');
        tab.html('');

      });

    $('#<?= $_accordion ?>-month')
      .on('event-add', function(e, p) {
        let tab = $('#<?= $_accordion ?>-month-tab');
        let date = _.dayjs(p.event.start);
        let edate = _.dayjs(p.event.end);
        let allDay = (date.unix() + 86400) == edate.unix() || date.format('YYYYMMDD') != edate.format('YYYYMMDD');

        let rowMaker = (p, date, edate, allDay) => {
          let row = $('<div class="form-row border" item></div>');
          // .css( 'color', !!p.feed.forecolor ? p.feed.forecolor : '#000')
          // .css( 'background-color', p.feed.color)
          row
            .addClass('pointer-calendar')
            .attr('style', 'border-left: 3px solid ' + p.feed.color + '!important')
            .data('data', p)
            .data('time', date.format('YYYY-MM-DD hh:mm'))
            .data('unix', date.unix())
            .data('allday', allDay ? 'yes' : 'no')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              let _me = $(this);
              let _data = _me.data();
              _data.originalEvent = e;
              $(document).trigger('calendar-event-click', _data);

            })
            .on('contextmenu', function(e) {
              if (e.shiftKey)
                return;

              e.stopPropagation();
              e.preventDefault();
              let _me = $(this);
              let _data = _me.data();
              _data.originalEvent = e;
              $(document).trigger('calendar-event-context', _data);

            });

          if (!allDay) {
            $('<div class="col-4 col-xl-3 py-1 text-truncate"></div>')
              .html(date.format('h:mma').replace(/m$/, ''))
              .appendTo(row);

          }

          $('<div class="col py-1 text-truncate"></div>')
            .attr('title', p.event.summary)
            .html(p.event.summary)
            .appendTo(row);

          return row;

        };

        let key = 'div[data-date="' + date.format('YYYY-MM-DD') + '"]';
        if (allDay) {
          let insertEvt = (p, date, edate, allDay, container) => {
            let items = $('> [item]', container);
            if (items.length > 0) {
              rowMaker(p, date, edate, allDay).insertBefore(items[0]);

            } else {
              rowMaker(p, date, edate, allDay).appendTo(container);

            }

          }

          insertEvt(p, date, edate, allDay, $(key, tab));
          // console.log( date.format('YYYY-MM-DD'), edate.format('YYYY-MM-DD HH:mm:ss'));
          if (date.format('YYYYMMDD') != edate.format('YYYYMMDD')) {
            for (let i = 1; i < 30; i++) {
              let _date = date.add(i, 'days');
              if (_date.format('YYYYMMDD') == edate.format('YYYYMMDD') && '0000' == edate.format('HHmm')) break;
              if (_date.format('YYYYMMDDHHmm') > edate.format('YYYYMMDDHHmm')) break;

              // console.log( _date.format('YYYY-MM-DD'));

              key = 'div[data-date="' + _date.format('YYYY-MM-DD') + '"]';
              insertEvt(p, date, edate, allDay, $(key, tab));

            }

          }

        } else {
          // insert at correct location
          let before = false;
          let container = $(key, tab);
          $('> [item]', container).each((i, row) => {
            let _row = $(row);
            let _data = _row.data();

            if ('yes' != _data.allDay) {
              if (date.unix() < _data.unix) {
                before = row;
                return false; // jQuery break

              }

            }

          });

          if (!!before) {
            rowMaker(p, date, edate, allDay).insertBefore(before);

          } else {
            rowMaker(p, date, edate, allDay).appendTo(container);

          }

        }

      })
      .on('update-tab', function(e) {
        let _me = $(this);
        let tab = $('#<?= $_accordion ?>-month-tab');
        let date = _.dayjs($('#<?= $_accordion ?>-date').val());
        let url = '<?= $this->route ?>/month?seed=' + date.format('YYYY-MM-DD');

        tab.load(url, html => {

          $('<button type="button" class="btn btn-light btn-sm"><i class="bi bi-chevron-double-left"></i></button>')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();

              $('#<?= $_accordion ?>-date').val(date.add('-1', 'month').format('YYYY-MM-DD'));
              _me.trigger('update-tab');

            })
            .prependTo($('[heading]', tab))

          $('<button type="button" class="btn btn-light btn-sm"><i class="bi bi-chevron-double-right"></i></button>')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();

              $('#<?= $_accordion ?>-date').val(date.add('1', 'month').format('YYYY-MM-DD'));
              _me.trigger('update-tab');

            })
            .appendTo($('[heading]', tab))

          $('div[data-date]', tab).each((i, el) => {
            let _me = $(el);
            let _data = _me.data();
            $('<i class="bi bi-calendar-date pointer ml-auto"></i>')
              .on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();

                $('#<?= $_accordion ?>-date').val(_data.date);
                $('#<?= $_accordion ?>-agenda').tab('show');

              })
              .appendTo($('[headline]', _me));

          });

          $('.bi', '#<?= $_accordion ?>-date-refresh').addClass('bi-spin');

          let feeds = $(document).data('active_feeds');
          let i = 0;
          let getNextFeed = () => {
            if (feeds.length > i) {
              getFeed(feeds[i++], _me)
                .then(getNextFeed);

            } else {
              $('.bi', '#<?= $_accordion ?>-date-refresh').removeClass('bi-spin');

            }

          };

          // console.table( 'update-tab-week');
          getNextFeed();

        });

      })
      .on('show.bs.tab', function(e) {
        $('#<?= $_accordion ?>-nav').removeClass('d-none');
        $('#<?= $_accordion ?>-date-last-monday, #<?= $_accordion ?>-date-next-monday, #<?= $_accordion ?>-date-next-monday-week').addClass('d-none');
        $(this).trigger('update-tab');

      })
      .on('hidden.bs.tab', function(e) {
        let tab = $('#<?= $_accordion ?>-agenda-month');
        tab.html('');

      });

    $('#<?= $_accordion ?>-tablist')
      .on('update-active-tab', function(e) {
        let activeTab = $('a.active', this);
        if (activeTab.length > 0) {
          activeTab.trigger('update-tab');

        } else {
          // console.log( 'update-active-tab');
          <?php
          if (isset($this->data->mode) && 'widget' == $this->data->mode) {
            printf('$(\'#%s-widget\').tab(\'show\')', $_accordion);
          } else {
            printf('$(\'#%s-agenda\').tab(\'show\')', $_accordion);
          } ?>;

        }

      });

    $('#<?= $_accordion ?>-date-refresh')
      .on('click', function(e) {
        e.stopPropagation();
        $('#<?= $_accordion ?>-tablist').trigger('update-active-tab');

      })

    $('#<?= $_accordion ?>-date')
      .on('last-monday', function(e) {
        let _me = $(this);
        let d = _.dayjs(_me.val());

        if (1 == d.day()) {
          _me.val(d.subtract(7, 'day').format('YYYY-MM-DD'));

        } else {
          _me.val(d.subtract(d.day() - 1, 'day').format('YYYY-MM-DD'));

        }

        // console.log( d.day(), td.format('llll'));

        $('#<?= $_accordion ?>-tablist').trigger('update-active-tab');

      })
      .on('next-monday', function(e) {
        let _me = $(this);
        let d = _.dayjs(_me.val());
        let td = d.add(7 - (d.day() - 1), 'day');
        _me.val(td.format('YYYY-MM-DD'));

        // console.log( d.day(), td.format('llll'));

        $('#<?= $_accordion ?>-tablist').trigger('update-active-tab');

      })
      .on('next-monday-week', function(e) {
        let _me = $(this);
        let d = _.dayjs(_me.val());
        let td = d.add(14 - (d.day() - 1), 'day');
        _me.val(td.format('YYYY-MM-DD'));

        // console.log( d.day(), td.format('llll'));

        $('#<?= $_accordion ?>-tablist').trigger('update-active-tab');

      });

    $('#<?= $_accordion ?>-date-last-monday')
      .on('click', function(e) {
        e.stopPropagation();
        $('#<?= $_accordion ?>-date').trigger('last-monday');

      });

    $('#<?= $_accordion ?>-date-next-monday')
      .on('click', function(e) {
        e.stopPropagation();
        $('#<?= $_accordion ?>-date').trigger('next-monday');

      });

    $('#<?= $_accordion ?>-date-next-monday-week')
      .on('click', function(e) {
        e.stopPropagation();
        $('#<?= $_accordion ?>-date').trigger('next-monday-week');

      });

    $(document).on('load-active-feeds', (e) => {
      _.post({
        url: _.url('<?= $this->route ?>'),
        data: {
          action: 'get-active-feeds'

        },

      }).then(d => {
        if ('ack' == d.response) {
          $(document).data('active_feeds', d.data);
          // console.log( 'load-active-feeds');
          $('#<?= $_accordion ?>-tablist').trigger('update-active-tab');

        } else {
          _.growl(d);

        }

      });

    });

    $(document).on('calendar-refresh', e => $('#<?= $_accordion ?>-tablist').trigger('update-active-tab'));
    $(document).ready(() => $(document).trigger('load-active-feeds'));

    // console.log( 'done ..');

  })(_brayworth_);
</script>