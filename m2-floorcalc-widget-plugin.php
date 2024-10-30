<?php
/*
Plugin Name: MeasureSquare Calculator Widget for Floors
Plugin URI: https://measuresquare.com/flooring-calculators/
Description: Adds a sidebar button widget that allows visitors to use the Measure Square online flooring calculator
Version: 1.0
Author: Allen Wang
Author URI: https://measuresquare.com/
License: GPL2
*/

// The widget class
class M2_Floor_Calc_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'M2_Floor_Calc_Widget',
			__( 'MeasureSquare Flooring Calculator', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'select'   => 'Position',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Dropdown ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Position', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'select' ); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'top: 50%;'        => __( 'select', 'text_domain' ),
				'top: 15%;' => __( 'Top Right', 'text_domain' ),
				'top: 50%;' => __( 'Middle Right', 'text_domain' ),
				'top: 85%;' => __( 'Bottom Right', 'text_domain' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select, $key, false ) . '>'. $name . '</option>';

			} ?>
			</select>
		</p>

	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['select']   = isset( $new_instance['select'] ) ? wp_strip_all_tags( $new_instance['select'] ) : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {

		extract( $args );

		// Check the widget options
		$select   = isset( $instance['select'] ) ? $instance['select'] : '';

		// WordPress core before_widget hook (always include )
		echo $before_widget;
        echo '<div id="m2-calcbutton-widget">';
        echo '<button id="calculateBtn" style="position: fixed; right: 0px; '.$select.' margin-top: -20px; width: 75px; height: 40px; padding: 2px 4px; outline: currentcolor none 0px; border: medium none; color: rgb(255, 255, 255); background-color: rgb(200, 22, 35); background-image: none; cursor: pointer; line-height: 16px;">Flooring
        Calculator</button>';        
        echo '</div>';

		// WordPress core after_widget hook (always include )
		echo $after_widget;

	}

}

// Register the widget
function m2floorcalc_register_custom_widget() {
	register_widget( 'M2_Floor_Calc_Widget' );
}

// Load calculator scripts
function m2floorcalc_add_scripts() {
    if ( ! wp_script_is( 'jquery', 'done' ) ) {
		wp_enqueue_script( 'jquery' );
	}
	wp_enqueue_script( 'm2-calculator-script', 'https://calculator.measuresquare.com/scripts/jquery-m2FlooringCalculator.js', array());
	wp_add_inline_script( 'm2-calculator-script', ' jQuery( document ).ready(function ($) {
							  jQuery(\'#calculateBtn\').m2Calculator({
                               measureSystem: "Imperial",
                               showCutSheet: false, // if false, will not include cutsheet section in return image
                               showDiagram: true,  // if false, will close the popup directly 
                               product: {},
                               cancel: function () {
                                   //when user closes the popup without calculation.
                               },
                               callback: function (data) {
                                   //json format, include user input, usage and base64image
                                   $("#callback").html(JSON.stringify(data));   
                                   console.log(data.input)
                                   $("#usageText").val(data.usage);    
                                   $("#image").attr("src", data.img);  //base64Image
                               } 
                              })
                            })' );
  }

add_action( 'wp_enqueue_scripts', 'm2floorcalc_add_scripts' );
add_action( 'widgets_init', 'm2floorcalc_register_custom_widget' );