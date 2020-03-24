<?php
/**
 * WL Import Users
 *
 * @package           WLImportUsers
 * @author            AstoSoft
 * @copyright         2020 AstoSoft Joanna Stach
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WL Import Users
 * Plugin URI:        https://astosoft.pl
 * Description:       Simple plugin to import users from the old CMS (from the tables).
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            AstoSoft
 * Author URI:        https://astosoft.pl
 * Text Domain:       wl-import-users
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( is_admin() ) {
  // we are in admin mode
  require_once __DIR__ . '/admin/wl-import-users-admin.php';
}