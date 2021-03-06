$(function() {
    $.widget( "ui.timespinner", $.ui.spinner, {
        options: {
            // seconds
            step: 60 * 1000,
            // hours
            page: 60
        },
 
        _parse: function( value ) {
            if ( typeof value === "string" ) {
                // already a timestamp
                if ( Number( value ) == value ) {
                    return Number( value );
                }
                return +Globalize.parseDate( value );
            }
            return value;
        },
 
        _format: function( value ) {
            return Globalize.format( new Date(value), "t" );
        }
    });	
    
    // radio button
	$('div.btn-group[data-toggle-name]').each(function() {
		var group   = $(this);
		var form    = group.parents('form').eq(0);
		var name    = group.attr('data-toggle-name');
		var hidden  = $('input[name="' + name + '"]', form);
		$('button', group).each(function(){
			var button = $(this);
			button.live('click', function(){
				hidden.val($(this).val());
			});
			if(button.val() == hidden.val()) {
				button.addClass('active');
			}
		});
	});
	
	// date pickers
	$( ".date-picker" ).datepicker();
	
	$( ".time-picker" ).timespinner();

	/* Attendees */
	
	$('#new-line').click(newLine);
	
	$('#attendees').delegate('.input-phone','keydown',function(e) {
		// create a new line when tab is pressed in the last input box
		if( !e.shiftKey && e.keyCode == 9 && $(this).parents('.line').next('.line').attr('id') == 'new-line' )
		{
			e.preventDefault();
			newLine();
			return false;
		}
	});
	
	$('#attendees').delegate('.deleteLine','click',function(e) {
		e.preventDefault();
	
		// cannot delete the last one
		if( $('#attendees .line').length == 1 )
			return false;
		
		// remove
		$(this).parents('.line').remove();

		return false;
	});

	function newLine()
	{
		$('#new-line').before( $('#attendeeLineTemplate').html() );
		
		$('input:first', $('#attendees .not-new:last')).focus();
	}	
});