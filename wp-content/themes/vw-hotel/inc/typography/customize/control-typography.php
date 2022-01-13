<?php
/**
 * Typography control class.
 *
 * @since  1.0.0
 * @access public
 */

class VW_Hotel_Control_Typography extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'typography';

	/**
	 * Array 
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $l10n = array();

	/**
	 * Set up our control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );

		// Make sure we have labels.
		$this->l10n = wp_parse_args(
			$this->l10n,
			array(
				'color'       => esc_html__( 'Font Color', 'vw-hotel' ),
				'family'      => esc_html__( 'Font Family', 'vw-hotel' ),
				'size'        => esc_html__( 'Font Size',   'vw-hotel' ),
				'weight'      => esc_html__( 'Font Weight', 'vw-hotel' ),
				'style'       => esc_html__( 'Font Style',  'vw-hotel' ),
				'line_height' => esc_html__( 'Line Height', 'vw-hotel' ),
				'letter_spacing' => esc_html__( 'Letter Spacing', 'vw-hotel' ),
			)
		);
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'vw-hotel-ctypo-customize-controls' );
		wp_enqueue_style(  'vw-hotel-ctypo-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {

			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : ''
			);

			if ( 'family' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_families();

			elseif ( 'weight' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();

			elseif ( 'style' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
		}
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>

		<# if ( data.family && data.family.choices ) { #>

			<li class="typography-font-family">

				<# if ( data.family.label ) { #>
					<span class="customize-control-title">{{ data.family.label }}</span>
				<# } #>

				<select {{{ data.family.link }}}>

					<# _.each( data.family.choices, function( label, choice ) { #>
						<option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.weight && data.weight.choices ) { #>

			<li class="typography-font-weight">

				<# if ( data.weight.label ) { #>
					<span class="customize-control-title">{{ data.weight.label }}</span>
				<# } #>

				<select {{{ data.weight.link }}}>

					<# _.each( data.weight.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.style && data.style.choices ) { #>

			<li class="typography-font-style">

				<# if ( data.style.label ) { #>
					<span class="customize-control-title">{{ data.style.label }}</span>
				<# } #>

				<select {{{ data.style.link }}}>

					<# _.each( data.style.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.size ) { #>

			<li class="typography-font-size">

				<# if ( data.size.label ) { #>
					<span class="customize-control-title">{{ data.size.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.size.link }}} value="{{ data.size.value }}" />

			</li>
		<# } #>

		<# if ( data.line_height ) { #>

			<li class="typography-line-height">

				<# if ( data.line_height.label ) { #>
					<span class="customize-control-title">{{ data.line_height.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.line_height.link }}} value="{{ data.line_height.value }}" />

			</li>
		<# } #>

		<# if ( data.letter_spacing ) { #>

			<li class="typography-letter-spacing">

				<# if ( data.letter_spacing.label ) { #>
					<span class="customize-control-title">{{ data.letter_spacing.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.letter_spacing.link }}} value="{{ data.letter_spacing.value }}" />

			</li>
		<# } #>

		</ul>
	<?php }

	/**
	 * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
	 *
	 * @todo Integrate with Google fonts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_fonts() { return array(); }

	/**
	 * Returns the available font families.
	 *
	 * @todo Pull families from `get_fonts()`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_font_families() {

		return array(
			'' => __( 'No Fonts', 'vw-hotel' ),
        'Abril Fatface' => __( 'Abril Fatface', 'vw-hotel' ),
        'Acme' => __( 'Acme', 'vw-hotel' ),
        'Anton' => __( 'Anton', 'vw-hotel' ),
        'Architects Daughter' => __( 'Architects Daughter', 'vw-hotel' ),
        'Arimo' => __( 'Arimo', 'vw-hotel' ),
        'Arsenal' => __( 'Arsenal', 'vw-hotel' ),
        'Arvo' => __( 'Arvo', 'vw-hotel' ),
        'Alegreya' => __( 'Alegreya', 'vw-hotel' ),
        'Alfa Slab One' => __( 'Alfa Slab One', 'vw-hotel' ),
        'Averia Serif Libre' => __( 'Averia Serif Libre', 'vw-hotel' ),
        'Bangers' => __( 'Bangers', 'vw-hotel' ),
        'Boogaloo' => __( 'Boogaloo', 'vw-hotel' ),
        'Bad Script' => __( 'Bad Script', 'vw-hotel' ),
        'Bitter' => __( 'Bitter', 'vw-hotel' ),
        'Bree Serif' => __( 'Bree Serif', 'vw-hotel' ),
        'BenchNine' => __( 'BenchNine', 'vw-hotel' ),
        'Cabin' => __( 'Cabin', 'vw-hotel' ),
        'Cardo' => __( 'Cardo', 'vw-hotel' ),
        'Courgette' => __( 'Courgette', 'vw-hotel' ),
        'Cherry Swash' => __( 'Cherry Swash', 'vw-hotel' ),
        'Cormorant Garamond' => __( 'Cormorant Garamond', 'vw-hotel' ),
        'Crimson Text' => __( 'Crimson Text', 'vw-hotel' ),
        'Cuprum' => __( 'Cuprum', 'vw-hotel' ),
        'Cookie' => __( 'Cookie', 'vw-hotel' ),
        'Chewy' => __( 'Chewy', 'vw-hotel' ),
        'Days One' => __( 'Days One', 'vw-hotel' ),
        'Dosis' => __( 'Dosis', 'vw-hotel' ),
        'Droid Sans' => __( 'Droid Sans', 'vw-hotel' ),
        'Economica' => __( 'Economica', 'vw-hotel' ),
        'Fredoka One' => __( 'Fredoka One', 'vw-hotel' ),
        'Fjalla One' => __( 'Fjalla One', 'vw-hotel' ),
        'Francois One' => __( 'Francois One', 'vw-hotel' ),
        'Frank Ruhl Libre' => __( 'Frank Ruhl Libre', 'vw-hotel' ),
        'Gloria Hallelujah' => __( 'Gloria Hallelujah', 'vw-hotel' ),
        'Great Vibes' => __( 'Great Vibes', 'vw-hotel' ),
        'Handlee' => __( 'Handlee', 'vw-hotel' ),
        'Hammersmith One' => __( 'Hammersmith One', 'vw-hotel' ),
        'Inconsolata' => __( 'Inconsolata', 'vw-hotel' ),
        'Indie Flower' => __( 'Indie Flower', 'vw-hotel' ),
        'IM Fell English SC' => __( 'IM Fell English SC', 'vw-hotel' ),
        'Julius Sans One' => __( 'Julius Sans One', 'vw-hotel' ),
        'Josefin Slab' => __( 'Josefin Slab', 'vw-hotel' ),
        'Josefin Sans' => __( 'Josefin Sans', 'vw-hotel' ),
        'Kanit' => __( 'Kanit', 'vw-hotel' ),
        'Lobster' => __( 'Lobster', 'vw-hotel' ),
        'Lato' => __( 'Lato', 'vw-hotel' ),
        'Lora' => __( 'Lora', 'vw-hotel' ),
        'Libre Baskerville' => __( 'Libre Baskerville', 'vw-hotel' ),
        'Lobster Two' => __( 'Lobster Two', 'vw-hotel' ),
        'Merriweather' => __( 'Merriweather', 'vw-hotel' ),
        'Monda' => __( 'Monda', 'vw-hotel' ),
        'Montserrat' => __( 'Montserrat', 'vw-hotel' ),
        'Muli' => __( 'Muli', 'vw-hotel' ),
        'Marck Script' => __( 'Marck Script', 'vw-hotel' ),
        'Noto Serif' => __( 'Noto Serif', 'vw-hotel' ),
        'Open Sans' => __( 'Open Sans', 'vw-hotel' ),
        'Overpass' => __( 'Overpass', 'vw-hotel' ),
        'Overpass Mono' => __( 'Overpass Mono', 'vw-hotel' ),
        'Oxygen' => __( 'Oxygen', 'vw-hotel' ),
        'Orbitron' => __( 'Orbitron', 'vw-hotel' ),
        'Patua One' => __( 'Patua One', 'vw-hotel' ),
        'Pacifico' => __( 'Pacifico', 'vw-hotel' ),
        'Padauk' => __( 'Padauk', 'vw-hotel' ),
        'Playball' => __( 'Playball', 'vw-hotel' ),
        'Playfair Display' => __( 'Playfair Display', 'vw-hotel' ),
        'PT Sans' => __( 'PT Sans', 'vw-hotel' ),
        'Philosopher' => __( 'Philosopher', 'vw-hotel' ),
        'Permanent Marker' => __( 'Permanent Marker', 'vw-hotel' ),
        'Poiret One' => __( 'Poiret One', 'vw-hotel' ),
        'Quicksand' => __( 'Quicksand', 'vw-hotel' ),
        'Quattrocento Sans' => __( 'Quattrocento Sans', 'vw-hotel' ),
        'Raleway' => __( 'Raleway', 'vw-hotel' ),
        'Rubik' => __( 'Rubik', 'vw-hotel' ),
        'Rokkitt' => __( 'Rokkitt', 'vw-hotel' ),
        'Russo One' => __( 'Russo One', 'vw-hotel' ),
        'Righteous' => __( 'Righteous', 'vw-hotel' ),
        'Slabo' => __( 'Slabo', 'vw-hotel' ),
        'Source Sans Pro' => __( 'Source Sans Pro', 'vw-hotel' ),
        'Shadows Into Light Two' => __( 'Shadows Into Light Two', 'vw-hotel'),
        'Shadows Into Light' => __( 'Shadows Into Light', 'vw-hotel' ),
        'Sacramento' => __( 'Sacramento', 'vw-hotel' ),
        'Shrikhand' => __( 'Shrikhand', 'vw-hotel' ),
        'Tangerine' => __( 'Tangerine', 'vw-hotel' ),
        'Ubuntu' => __( 'Ubuntu', 'vw-hotel' ),
        'VT323' => __( 'VT323', 'vw-hotel' ),
        'Varela Round' => __( 'Varela Round', 'vw-hotel' ),
        'Vampiro One' => __( 'Vampiro One', 'vw-hotel' ),
        'Vollkorn' => __( 'Vollkorn', 'vw-hotel' ),
        'Volkhov' => __( 'Volkhov', 'vw-hotel' ),
        'Yanone Kaffeesatz' => __( 'Yanone Kaffeesatz', 'vw-hotel' )
		);
	}

	/**
	 * Returns the available font weights.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_weight_choices() {

		return array(
			'' => esc_html__( 'No Fonts weight', 'vw-hotel' ),
			'100' => esc_html__( 'Thin',       'vw-hotel' ),
			'300' => esc_html__( 'Light',      'vw-hotel' ),
			'400' => esc_html__( 'Normal',     'vw-hotel' ),
			'500' => esc_html__( 'Medium',     'vw-hotel' ),
			'700' => esc_html__( 'Bold',       'vw-hotel' ),
			'900' => esc_html__( 'Ultra Bold', 'vw-hotel' ),
		);
	}

	/**
	 * Returns the available font styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_style_choices() {

		return array(
			'normal'  => esc_html__( 'Normal', 'vw-hotel' ),
			'italic'  => esc_html__( 'Italic', 'vw-hotel' ),
			'oblique' => esc_html__( 'Oblique', 'vw-hotel' )
		);
	}
}
