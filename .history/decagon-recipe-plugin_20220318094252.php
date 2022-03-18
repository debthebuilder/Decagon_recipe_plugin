<?php
/**
* @package DecagonRecipePlugin
*/
/*
Plugin Name: Decagon Recipe Plugin
Plugin URI: https://decagonhq.com/
Description:A simple plugin that can be used to create a new recipe, view all available recipes, to edit a recipe and to delete a single or multiple recipes
Version: 1.0.0
Author: Valentine Michael
Author URI: https://chionye.com/
License: GPLv2 or later
Text Domain: decagon-recipe-plugin
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
Copyright 2005-2015 Automattic, Inc
*/

defined( 'ABSPATH' ) or die();


register_activation_hook( __FILE__, 'crudOperationsTable');

function crudOperationsTable() {
  global $wpdb;
  $charsetCollate = $wpdb->get_charset_collate();
  $tableName = $wpdb->prefix . 'recipe';
  $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `recipe` text DEFAULT NULL,
  PRIMARY KEY(id)
  ) $charsetCollate;";
  if ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

add_action('admin_menu', 'addAdminPages');

function addAdminPages() {
    add_menu_page('Recipes', 'Recipes', 'manage_options', 'Recipe', '', 'dashicons-buddicons-community');
    add_submenu_page( 'Recipe', 'All Recipes', 'All Recipes',
        'manage_options', 'Recipe', 'Recipe');
        add_submenu_page( 'Recipe', 'Add Recipes', 'Add Recipes',
        'manage_options', 'Recipe', 'Recipe');
}

function getAllRecords()
{
	global $wpdb;
	$tableName = $wpdb->prefix . 'recipe';
	$i = 1;
	$result = $wpdb->get_results("SELECT * FROM $tableName");
	foreach($result as $print) {
	echo "
	<table class='wp-list-table widefat striped'>
	<thead>
		<tr>
		<th width='25%'>No</th>
		<th width='25%'>Name</th>
		<th width='25%'>Ingredients</th>
		<th width='25%'>Recipe</th>
		<th width='25%'>Edit</th>
		<th width='25%'>Delete</th>
		</tr>
	</thead>
	<tbody>
		<form action='' method='post'>
		<tr>
			<td width='25%'>$i++</td>
			<td width='25%'>$print->name</td>
			<td width='25%'>$print->ingredients</td>
			<td width='25%'>$print->recipe</td>
			<td width='25%'><a href='admin.php?page=Recipe&id=$print->id'>Edit</a></td>
			<td width='25%'><a href='#'>Delete</a></td>
		</tr>
		</form>
	</tbody>
	</table>";
	}
?>
</div>
<?php 
}

function getAddRecords()
{
?>
	<style>
	div {
		margin-bottom:2px;
	}
		
	input{
		margin-bottom:4px;
	}
	</style>

	<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
	<div>
	<label for="username">Food Name <strong>*</strong></label>
	<input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '">
	</div>
		
	<div>
	<label for="website">Food Ingredients</label>
	<input type="text" name="website" value="' . ( isset( $_POST['website']) ? $website : null ) . '">
	</div>
		
	<div>
	<label for="firstname">First Name</label>
	<input type="text" name="fname" value="' . ( isset( $_POST['fname']) ? $first_name : null ) . '">
	</div>
		
	<div>
	<label for="website">Last Name</label>
	<input type="text" name="lname" value="' . ( isset( $_POST['lname']) ? $last_name : null ) . '">
	</div>
		
	<div>
	<label for="nickname">Nickname</label>
	<input type="text" name="nickname" value="' . ( isset( $_POST['nickname']) ? $nickname : null ) . '">
	</div>
		
	<div>
	<label for="bio">About / Bio</label>
	<textarea name="bio">' . ( isset( $_POST['bio']) ? $bio : null ) . '</textarea>
	</div>
	<input type="submit" name="submit" value="Register"/>
	</form>
	';
}