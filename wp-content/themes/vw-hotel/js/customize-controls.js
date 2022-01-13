( function( api ) {

	// Extends our custom "vw-hotel" section.
	api.sectionConstructor['vw-hotel'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );