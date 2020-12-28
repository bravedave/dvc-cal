/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

( _ => {
  $(document).on( 'edit-calendar-event', (e, event) => {
    console.log( event);

  });

  $(document).ready( () => console.log( 'hi'));

})( _brayworth_);
