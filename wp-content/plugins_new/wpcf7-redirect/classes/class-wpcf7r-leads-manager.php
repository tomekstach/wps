<?php
/**
 * Class WPCF7R_Leads_Manager - Container class that handles leads management
 */

defined( 'ABSPATH' ) || exit;

class WPCF7R_Leads_Manager {
	/**
	 * Save a reference to the last lead inserted to the DB
	 */
	public static $new_lead_id;

	/**
	 * Define the leads post type
	 */
	public static $post_type = 'wpcf7r_leads';

	public function __construct( $cf7_id ) {
		$this->cf7_id = $cf7_id;

		$this->leads = array();

	}

	/**
	 * Get the leads post type
	 */
	public static function get_post_type() {
		return self::$post_type;
	}

	/**
	 * Add A select filter on edit.php screen to filter records by form
	 */
	public static function add_form_filter() {
		if ( isset( $_GET['post_type'] ) && self::get_post_type() === $_GET['post_type'] ) {
			$values = array();

			$forms = get_posts(
				array(
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'post_type'      => 'wpcf7_contact_form',
				)
			);

			foreach ( $forms as $form ) :
				$values[ $form->post_title ] = $form->ID;
			endforeach;

			?>

			<select name="cf7_form">
				<option value=""><?php _e( 'Form', 'wpcf7-redirect' ); ?></option>
				<?php
					$current_v = isset( $_GET['cf7_form'] ) ? (int) $_GET['cf7_form'] : '';

				foreach ( $values as $label => $value ) {
					printf(
						'<option value="%s"%s>%s</option>',
						$value,
						$value === $current_v ? ' selected="selected"' : '',
						$label
					);
				}
				?>
			</select>

			<?php
		}
	}

	/**
	 * Search by filters
	 */
	public static function filter_request_query( $query ) {
		//modify the query only if it admin and main query.
		if ( ! ( is_admin() && $query->is_main_query() ) ) {
			return $query;
		}

		//we want to modify the query for the targeted custom post and filter option
		if ( ! isset( $query->query['post_type'] ) || ( ! ( self::get_post_type() === $query->query['post_type'] && isset( $_REQUEST['cf7_form'] ) ) ) ) {
			return $query;
		}

		//for the default value of our filter no modification is required
		if ( 0 === (int) $_REQUEST['cf7_form'] ) {
			return $query;
		}
		//modify the query_vars.

		$posted_value = isset( $_REQUEST['cf7_form'] ) && (int) $_REQUEST['cf7_form'] ? (int) $_REQUEST['cf7_form'] : '';

		$meta_query = $query->get( 'meta_query' );

		if ( ! $meta_query ) {
			$meta_query = array();
		}

		$meta_query[] = array(
			array(
				'key'     => 'cf7_form',
				'value'   => $posted_value,
				'compare' => '=',
			),
		);

		$query->set( 'meta_query', $meta_query );

		return $query;
	}

	/**
	 * Initialize leads table tab
	 */
	public function init() {
		include( WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'leads . php' );
	}

	/**
	 * get the url to the admin post type list
	 * Auto filter by selected action
	 */
	public static function get_admin_url( $form_id ) {
		$url = admin_url( 'edit.php?post_type=' . self::get_post_type() );

		return add_query_arg( 'cf7_form', $form_id, $url );
	}

	/**
	 * Get leads
	 */
	public function get_leads() {
		$args = array(
			'post_type'      => self::get_post_type(),
			'post_status'    => 'private',
			'posts_per_page' => 20,
			'meta_query'     => array(
				array(
					'key'   => 'cf7_form',
					'value' => $this->cf7_id,
				),
			),
		);

		$leads_posts = get_posts( $args );

		if ( $leads_posts ) {
			foreach ( $leads_posts as $leads_post ) {
				$lead = new WPCF7R_Lead( $leads_post );

				$this->leads[] = $lead;
			}
		}

		return $this->leads;
	}
	/**
	 * Insert new lead
	 */
	public static function insert_lead( $cf7_form_id, $args, $files = array(), $lead_type, $action_id ) {
		$args['cf7_form']      = $cf7_form_id;
		$args['cf7_action_id'] = $action_id;

		$contact_form_title = get_the_title( $cf7_form_id );

		$new_post = array(
			'post_type'   => self::get_post_type(),
			'post_status' => 'private',
			'post_title'  => __( 'Lead from contact form: ', 'wpcf7-redirect' ) . $contact_form_title,
		);

		self::$new_lead_id = wp_insert_post( $new_post );

		$lead = new WPCF7R_Lead( self::$new_lead_id );

		$lead->update_lead_data( $args );

		$lead->update_lead_files( $files );

		$lead->update_lead_type( $lead_type );

		return $lead;
	}

	/**
	 * Save the action to the db lead
	 * @param  $lead_id
	 * @param  $action_name
	 * @param  $details
	 */
	public static function save_action( $lead_id, $action_name, $details ) {
		add_post_meta( $lead_id, 'action - ' . $action_name, $details );
	}

	/**
	 * Get a single action row
	 */
	public function get_lead_row( $lead ) {
		ob_start();
		do_action( 'before_wpcf7r_lead_row', $this );
		?>

		<tr class="primary" data-postid="<?php echo $lead->get_id(); ?>">
			<td class="manage-column column-primary sortable desc edit column-id">
				<?php echo $lead->get_id(); ?>
				<div class="row-actions">
					<span class="edit">
						<a href="<?php echo get_edit_post_link( $lead->get_id() ); ?>" data-id="<?php echo $lead->get_id(); ?>" aria-label="<?php _e( 'View', 'wpcf7-redirect' ); ?>" target="_blank"><?php _e( 'View', 'wpcf7-redirect' ); ?></a> |
					</span>
					<span class="trash">
						<a href="#" class="submitdelete" data-id="<?php echo $lead->get_id(); ?>" aria-label="<?php _e( 'Move to trash', 'wpcf7-redirect' ); ?>"><?php _e( 'Move to trash', 'wpcf7-redirect' ); ?></a> |
					</span>
					<?php do_action( 'wpcf7r_after_lead_links', $lead ); ?>
				</div>
			</td>
			<td class="manage-column column-primary sortable desc edit column-date">
				<?php echo $lead->get_date(); ?>
			</td>
			<td class="manage-column column-primary sortable desc edit column-time"><?php echo $lead->get_time(); ?></td>
			<td class="manage-column column-primary sortable desc edit column-type"><?php echo $lead->get_lead_type(); ?></td>
			<td></td>
		</tr>

		<?php
		do_action( 'after_wpcf7r_lead_row', $this );

		return apply_filters( 'wpcf7r_get_lead_row', ob_get_clean(), $this );
	}
}
