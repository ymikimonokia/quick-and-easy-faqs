<?php

namespace Quick_And_Easy_Faqs\Includes;

/**
 * This class holds all the faqs query related members and methods.
 */
class Faqs_Query {

	/**
	 * Holds the query of faqs.
	 *
	 * @var array $faqs_query
	 */
	protected $faqs_query;

	/**
	 * Holds the value of display type of faqs.
	 *
	 * @var string $display
	 */
	protected $display;

	/**
	 * The filters of this plugin.
	 *
	 * @var bool | array $filters
	 */
	protected $filters;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string       $display Display type of plugin.
	 * @param bool | array $filters The filters of this plugin.
	 */
	public function __construct( $display = '', $filters = false || [] ) {

		$this->display = $display;
		$this->filters = $filters;

		if ( $this->filters && ! is_array( $this->filters ) ) {

			$terms = get_terms(
				[
					'taxonomy' => 'faq-group',
					'fields'   => 'slugs',
				]
			);

			$this->filters = $terms;
		}


		$this->faqs_query = $this->query_build();
	}

	/**
	 * Build the basic query for faqs.
	 */
	protected function query_build() {



		$query = [
			'post_type'      => 'faq',
			'posts_per_page' => - 1,
		];

		if ( $this->filters ) {

			$query['tax_query'] = [
				[
					'taxonomy' => 'faq-group',
					'field'    => 'slug',
					'terms'    => $this->filters,
				],
			];
		}

		return $query;
	}

	/**
	 * Build post terms slugs array
	 *
	 * @param int $id Holds the faq post ID.
	 *
	 * @return array terms array.
	 */
	protected function get_terms_slugs( $id ) {
		$terms_slugs = [];
		$terms       = get_the_terms( $id, 'faq-group' );

		if ( $terms && ! is_wp_error( $terms ) ) {

			foreach ( $terms as $term ) {
				$terms_slugs[] = $term->slug;
			}
		}

		return $terms_slugs;
	}

	/**
	 * Build and render the faqs
	 *
	 * @param array $faq_terms_posts Holds the faq terms as slug and faqs ids as associative array.
	 */
	protected function build_titles_structure( $faq_terms_posts ) {

		echo '<div id="qe-faqs-index" class="qe-faqs-index"><ol class="qe-faqs-index-list">';

		foreach ( $faq_terms_posts as $slug => $faq_ids ) {

			if ( is_array( $faq_ids ) ) {

				echo '<h4 class="qe-faqs-group-title">' . esc_html( ucwords( str_replace( '-', ' ', $slug ) ) ) . '</h4>';

				foreach ( $faq_ids as $id ) {

					$terms_slugs = $this->get_terms_slugs( $id );
					?>
					<li class="<?php echo esc_attr( implode( ' ', $terms_slugs ) ); ?>">
						<a href="#qaef-<?php echo esc_attr( $id ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a>
					</li>
					<?php
				}
			} else {
				$terms_slugs = $this->get_terms_slugs( $faq_ids );
				?>
				<li class="<?php echo esc_attr( implode( ' ', $terms_slugs ) ); ?>">
					<a href="#qaef-<?php echo esc_attr( $faq_ids ); ?>"><?php echo esc_html( get_the_title( $faq_ids ) ); ?></a>
				</li>
				<?php
			}
		}
		echo '</ol></div>';
	}

	/**
	 * Render the faqs title
	 *
	 * @param array $faqs_posts_ids Faqs posts ids.
	 */
	protected function render_faqs_title( $faqs_posts_ids ) {

		if ( 'grouped' === $this->display ) {

			$faq_terms_posts = [];

			foreach ( $faqs_posts_ids as $id ) {

				$terms = get_the_terms( $id, 'faq-group' );

				if ( $terms && ! is_wp_error( $terms ) ) {

					foreach ( $terms as $term ) {
						$faq_terms_posts[ $term->slug ][] = $id;
					}
				}
			}

			$this->build_titles_structure( $faq_terms_posts );

		} elseif ( '' === $this->display ) {

			$this->build_titles_structure( $faqs_posts_ids );
		}
	}

	/**
	 * Build and render the faqs filters
	 */
	protected function build_faqs_filter_structure() {

		if ( $this->filters ) {
			?>
			<ul class="qe-faqs-filters-container">
				<li><a class="qe-faqs-filter" href="#" data-filter="*"><?php esc_html_e( 'All', 'quick-and-easy-faqs' ); ?></a></li>
				<?php
				foreach ( $this->filters as $term ) {
					echo '<li><a class="qe-faqs-filter" href="#' . esc_attr( $term ) . '" data-filter=".' . esc_attr( $term ) . '">' . esc_html( ucwords( str_replace( '-', ' ', $term ) ) ) . '</a></li>';
				}
				?>
			</ul>
			<?php
		}
	}

	/**
	 * Get and render the faqs icon
	 *
	 * @return string HTML.
	 */
	protected function get_the_icon() {

		if ( empty( $this->display ) || 'grouped' === $this->display ) {
			$class = 'fa fa-question-circle';
		} else {
			$class = 'fa fa-minus-circle';
		}

		return '<i class="' . esc_attr( $class ) . '"></i> ';
	}

	/**
	 * Build and render the faqs
	 *
	 * @param string $id Html Post ID.
	 * @param string $class Html class.
	 */
	protected function build_faqs_structure( $id, $class = '' ) {

		$terms_slugs = $this->get_terms_slugs( $id );

		if ( 'accordion' === $class || 'grouped-accordion' === $class || 'grouped-toggle' === $class ) {
			$class = 'toggle';
		} elseif ( empty( $class ) ) {
			$class = 'list';
		}
		?>
		<div id="qaef-<?php echo esc_attr( $id ); ?>" class="qe-faq-<?php echo esc_attr( $class ) . ' ' . esc_attr( implode( ' ', $terms_slugs ) ); ?>">
			<div class="qe-<?php echo esc_attr( $class ); ?>-title">
				<h4>
					<?php
					echo wp_kses( $this->get_the_icon(), [ 'i' => [ 'class' => [] ] ] );
					echo esc_html( get_the_title( $id ) );
					?>
				</h4>
			</div>
			<div class="qe-<?php echo esc_attr( $class ); ?>-content"><?php echo wp_kses_post( get_the_content( $id ) ); ?></div>
		</div>
		<?php
	}

	/**
	 * Render the faqs
	 */
	public function render() {

		$faq_posts = new \WP_Query( $this->faqs_query );

		if ( $faq_posts->have_posts() ) :

			$faqs_array     = $faq_posts->posts;
			$faqs_posts_ids = wp_list_pluck( $faqs_array, 'ID' );

			$this->build_faqs_filter_structure();

			$this->render_faqs_title( $faqs_posts_ids );

			while ( $faq_posts->have_posts() ) :

				$faq_posts->the_post();

				$this->build_faqs_structure( get_the_ID(), $this->display );

			endwhile;

		endif;

		// All the custom loops ends here so reset the query.
		wp_reset_query();
	}

}