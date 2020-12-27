<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

// sys::dump( $this->data->feed, null, true);
$date = '';
foreach ($this->data->feed as $event) {
  $d = new DateTime( $event['start']);
  if ( $d->format('Y-m-d') != $date) {
    $date = $d->format('Y-m-d');
    ?>
    <div class="row mb-2">
      <div class="col h5 m-0">
        <?= $d->format( 'D, M j') ?>

      </div>

    </div>
  <?php
  }
  $de = new DateTime( $event['end']);
  ?>
  <div class="row mb-2">
    <div class="col-3 col-md-2"><?= $d->format( 'g:i a') ?></div>
    <div class="col-3 col-md-2"><?= $de->format( 'g:i a') ?></div>
    <div class="col-6 col-md-8"><?= $event['summary'] ?></div>

  </div>

<?php
}