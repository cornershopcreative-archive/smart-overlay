<?php
/**
 * Helper methods for remove and restoring filters (mostly taken from buddypress)
 * PHP Version 5
 *
 * @since 0.8.2
 * @package Smart_Overlay
 * @author Cornershop Creative <devs@cshp.co>
 */
class Smart_Overlay_Filters {
	/**
	 * Remove all Filters
	 *
	 * @link http://hookr.io/functions/bp_remove_all_filters/
	 * @param string $tag WP filter to remove.
	 * @param bool   $priority WP Filter priority.
	 *
	 * @return bool
	 */
	public static function smart_overlay_remove_all_filters( $tag, $priority = false ) {
		global $wp_filter, $merged_filters;

		$filters = new stdClass();

		// Filters exist.
		if ( isset( $wp_filter[ $tag ] ) ) {

			// Filters exist in this priority.
			if ( ! empty( $priority ) && isset( $wp_filter[ $tag ][ $priority ] ) ) {

				// Store filters in a backup.
				$filters->wp_filter[ $tag ][ $priority ] = $wp_filter[ $tag ][ $priority ];

				// Unset the filters.
				unset( $wp_filter[ $tag ][ $priority ] );

				// Priority is empty.
			} else {

				// Store filters in a backup.
				$filters->wp_filter[ $tag ] = $wp_filter[ $tag ];

				// Unset the filters.
				unset( $wp_filter[ $tag ] );
			}
		}//end if

		// Check merged filters.
		if ( isset( $merged_filters[ $tag ] ) ) {

			// Store filters in a backup.
			$filters->merged_filters[ $tag ] = $merged_filters[ $tag ];

			// Unset the filters.
			unset( $merged_filters[ $tag ] );
		}

		return true;
	}

	/**
	 * Restore All Filters
	 *
	 * @link http://hookr.io/functions/bp_restore_all_filters/
	 * @param string $tag WP filter to remove.
	 * @param bool   $priority WP Filter priority.
	 *
	 * @return bool
	 */
	public static function smart_overlay_restore_all_filters( $tag, $priority = false ) {
		global $wp_filter, $merged_filters;

		$filters = new stdClass();

		// Filters exist.
		if ( isset( $filters->wp_filter[ $tag ] ) ) {

			// Filters exist in this priority.
			if ( ! empty( $priority ) && isset( $filters->wp_filter[ $tag ][ $priority ] ) ) {

				// Store filters in a backup.
				$wp_filter[ $tag ][ $priority ] = $filters->wp_filter[ $tag ][ $priority ];

				// Unset the filters.
				unset( $filters->wp_filter[ $tag ][ $priority ] );

				// Priority is empty.
			} else {

				// Store filters in a backup.
				$wp_filter[ $tag ] = $filters->wp_filter[ $tag ];

				// Unset the filters.
				unset( $filters->wp_filter[ $tag ] );
			}
		}//end if

		// Check merged filters.
		if ( isset( $filters->merged_filters[ $tag ] ) ) {

			// Store filters in a backup.
			$merged_filters[ $tag ] = $filters->merged_filters[ $tag ];

			// Unset the filters.
			unset( $filters->merged_filters[ $tag ] );
		}

		return true;
	}
}
