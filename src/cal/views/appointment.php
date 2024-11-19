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

use strings;
use theme;  ?>
<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <input type="hidden" name="action" value="appointment-save">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>"
    aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header <?= theme::modalHeader() ?> py-2">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>

        <div class="modal-body">

          <div class="row g-2 mb-2">

            <div class="col mb-2 mb-md-0">

              <input type="date" class="form-control" name="date" required>
            </div>

            <div class="col-md">

              <div class="row g-2">

                <div class="col">

                  <div class="input-group">

                    <input type="text" class="form-control" name="start"
                      placeholder="start" required>
                    <div class="input-group-text">-</div>
                    <input type="text" class="form-control" name="end"
                      placeholder="end" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-2 mb-2">
            <div class="col">
              <input type="text" name="subject" class="form-control" placeholder="subject"
                required>

            </div>

          </div>

          <div class="row g-2 mb-2">
            <div class="col">
              <input type="text" name="location" placeholder="location"
                class="form-control">

            </div>

          </div>

          <div class="row g-2 mb-2">
            <div class="col">
              <textarea name="notes" placeholder="notes ..." rows="3"
                class="form-control"></textarea>

            </div>

          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary"
            data-bs-dismiss="modal">close</button>
          <button type="submit" class="btn btn-primary">Save</button>

        </div>

      </div>

    </div>

  </div>
  <script>
    (_ => {
      const form = $('#<?= $_form ?>');
      const modal = $('#<?= $_modal ?>');

      form.find('input[name="start"]').on('change', function(e) {

        CheckTimeFormat.call(this);

        const s = $(this).val();
        if (s == '') return;

        const j = timeHandler(s);
        j.Minutes += 30;
        form.find('input[name="end"]').val(j.toString());
      });

      form.find('input[name="end"]').on('change', CheckTimeFormat);

      modal.on('shown.bs.modal', () => {

        form.on('submit', function(e) {

          let _form = $(this);
          let _data = _form.serializeFormJSON();

          _.fetch.post.form(_.url('<?= $this->route ?>'), this).then(d => {

            if ('ack' == d.response) {

              modal.trigger('success');
            } else {

              _.growl(d);
            }

            modal.modal('hide');
          });

          // console.table( _data);

          return false;
        });
      })
    })(_brayworth_);
  </script>
</form>