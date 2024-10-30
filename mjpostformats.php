<?php
 /*
	Plugin Name:  MJPostFormats
	Description: 	Used to create the new post formats.
  Version:      1.0.1
  Author:       Morgan JOURDIN
	Author URI:   http://www.morgan-jourdin.fr/
	Text Domain:  mjpostformats
	Domain Path: 	/languages
	License:      GPL2
 	License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'MJPOSTFORMATS_VERSION', '1.0.1' );
define( 'MJPOSTFORMATS_MINIMUM_WP_VERSION', '3.7' );
define( 'MJPOSTFORMATS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( MJPOSTFORMATS_PLUGIN_DIR . 'class.mjpostformats.php' );

$mjpostformats = new MJPostFormats;

register_activation_hook( __FILE__, array( $mjpostformats, 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( $mjpostformats, 'plugin_desactivation' ) );
?>
