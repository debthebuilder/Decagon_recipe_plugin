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

// create recipe table
function crudOperationsTable() {
  global $wpdb;
  $charsetCollate = $wpdb->get_charset_collate();
  $tableName = $wpdb->prefix . 'recipe';
  $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_name` varchar(255) DEFAULT NULL,
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
        'manage_options', 'Recipe', 'getAllRecords');
        add_submenu_page( 'Recipe', 'Add Recipes', 'Add Recipes',
        'manage_options', 'AddRecipe', 'getAllRecords');
}

// get all recipes
function getAllRecords()
{
	global $wpdb;
	$tableName = $wpdb->prefix . 'recipe';
	$i = 1;
	
	if ($_GET['page'] == "Recipe" && isset($_GET['del'])) {
		
		$id = $_GET['del'];
		$wpdb->query("DELETE FROM $tableName WHERE id='$id'");
		echo "<script>location.replace('admin.php?page=Recipe');</script>";
	}elseif($_GET['page'] == "Recipe" && isset($_GET['edit'])){
		getEditRecords();
	}elseif($_GET['page'] == "AddRecipe"){
		getAddRecords();
	}else{
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
			<td width='25%'>$i</td>
			<td width='25%'>$print->recipe_name</td>
			<td width='25%'>$print->ingredients</td>
			<td width='25%'>$print->recipe</td>
			<td width='25%'><a href='admin.php?page=Recipe&edit=$print->id'>Edit</a></td>
			<td width='25%'><a href='admin.php?page=Recipe&del=$print->id'>Delete</a></td>
		</tr>
		</form>
	</tbody>
	</table>";
	$i++;
	}
}
?>
</div>
<?php 
}

// add a new recipe
function getAddRecords()
{
	if (isset($_POST['newrecipe'])) {
		global $wpdb;
		$tableName = $wpdb->prefix . 'recipe';
		$name = $_POST['name'];
		$ingredients = $_POST['ingredients'];
		$recipe = $_POST['recipe'];
		$wpdb->query("INSERT INTO $tableName (recipe_name,ingredients, recipe) VALUES('$name','$ingredients', '$recipe')");
		echo "<script>location.replace('admin.php?page=Recipe');</script>";
	}
	?>
	<style>
	div {
		margin-bottom:2px;
	}
		
	input, textarea{
		margin-bottom:4px;
		width:100%
	}
	</style>

	<form action="" method="post">
	<div>
	<label for="username">Food Name <strong>*</strong></label>
	<input type="text" name="name" required>
	</div>
		
	<div>
	<label for="ingredients">Food Ingredients<strong>*</strong></label>
	<textarea name="ingredients" required></textarea>
	</div>
	
	<div>
	<label for="bio">Recipe<strong>*</strong></label>
	<textarea name="recipe" required></textarea>
	</div>
	<input type="submit" name="newrecipe" value="Register"/>
	</form>
	<?php
}

// edit a recipe
function getEditRecords()
{
	global $wpdb;
	$tableName = $wpdb->prefix . 'recipe';
	if (isset($_POST['editrecipe'])) {
		
		$name = $_POST['name'];
		$ingredients = $_POST['ingredients'];
		$recipe = $_POST['recipe'];
		$id = $_POST['id'];
		$wpdb->query("UPDATE $tableName SET recipe_name='$name', ingredients='$ingredients', recipe='$recipe' WHERE id='$id'");
		echo "<script>location.replace('admin.php?page=Recipe');</script>";
	}
	$id = $_GET['edit'];
	$result = $wpdb->get_results("SELECT * FROM $tableName WHERE id='$id'");
	foreach($result as $print) {
	echo "
	<style>
	div {
		margin-bottom:2px;
	}
		
	input, textarea{
		margin-bottom:4px;
		width:100%
	}
	</style>

	<form action='' method='post'>
	<div>
	<label for='username'>Food Name <strong>*</strong></label>
	<input type='text' name='name' value='$print->name'>
	<input type='hidden' name='id' value='$print->id'>
	</div>
		
	<div>
	<label for='ingredients'>Food Ingredients</label>
	<textarea name='ingredients'>$print->ingredients</textarea>
	</div>
	
	<div>
	<label for='bio'>Recipe</label>
	<textarea name='recipe'>$print->recipe</textarea>
	</div>
	<input type='submit' name='editrecipe' value='Register'/>
	</form>
	";
	}
}