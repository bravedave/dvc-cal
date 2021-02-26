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

use strings;  ?>
<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <input type="hidden" name="action" value="appointment-save">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-secondary text-white py-2">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>

        <div class="modal-body">
          <div class="form-row mb-2">
            <div class="col mb-2 mb-md-0">
              <input type="date" class="form-control" name="date" required>

            </div>

            <div class="col-md">
              <div class="form-row">
                <div class="col">
                  <div class="input-group">
                    <input type="time" class="form-control" name="start" required>

                    <div class="input-group-append">
                      <div class="input-group-text">-</div>
                    </div>

                    <input type="time" class="form-control" name="end" required>

                  </div>

                </div>

              </div>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="col">
              <input type="text" name="subject" class="form-control" placeholder="subject" required>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="col">
              <input type="text" name="location" placeholder="location" class="form-control">

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="col">
              <textarea name="notes" placeholder="notes ..." rows="3" class="form-control"></textarea>

            </div>

          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">close</button>
          <button type="submit" class="btn btn-primary">Save</button>

        </div>

      </div>

    </div>

  </div>
  <script>
  ( _ => $(document).ready( () => {
    $('#<?= $_form ?>')
    .on( 'submit', function( e) {
      let _form = $(this);
      let _data = _form.serializeFormJSON();

      _.post({
        url : _.url('<?= $this->route ?>'),
        data : _data,

      }).then( d => {
        if ( 'ack' == d.response) {
          $('#<?= $_modal ?>').trigger( 'success');

        }
        else {
          _.growl( d);

        }

        $('#<?= $_modal ?>').modal( 'hide');

      });

      // console.table( _data);

      return false;

    });

  }))( _brayworth_);
  </script>
</form>