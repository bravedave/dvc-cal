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

  $(document).ready( () => console.info( "add an event hook to the page 'edit-calendar-event', and it will receive events when they are clicked"));

})( _brayworth_);
