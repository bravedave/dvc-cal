/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

( _ => {
  $(document).on( 'calendar-event-click', (e, event) => console.log( event));
  $(document).on( 'calendar-event-context', (e, event) => console.log( event));

  $(document).ready( () => console.info( "add an event hook to the page 'calendar-event-click', and it will receive events when they are clicked, add a css rule for pointer-calendar to change cursor on editable cells"));

})( _brayworth_);
