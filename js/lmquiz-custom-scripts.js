var lmquiz_admin;
( function( $ ){
	lmquiz_admin = {
		init: function() {
			console.log("hello");
			$( '.status-clone-wrapper' ).cloneya();
		},
	}
	$( document ).ready( function() { lmquiz_admin.init(); } );
})( jQuery );
