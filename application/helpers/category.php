<?php
/**
 * Category helper. Displays categories on the front-end.
 * 
 * @package    Category
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class category_Core {
	
	/**
	 * Generates pseudo-hierarchy tree, based on category naming convention.
	 */
	public static function generate_pseudo_tree(array $categories) {
		$sorted_categories = array();
		foreach ($categories as $cid => $category) {
				
			// Indent categories of type 1a., 2e., etc.
			$category_title = $category[0];
			$category_array = array(
				'cid' => $cid,
				'category' => $category,
			);
			if (is_numeric($category_title[0])) {
				if (ctype_alpha($category_title[1]) && array_key_exists('parent_'.$category_title[0], $sorted_categories)) {
					$sorted_categories['parent_'.$category_title[0]]['children'][] = $category_array;
				}
				else {
					$sorted_categories['parent_'.$category_title[0]] = $category_array;
					$sorted_categories['parent_'.$category_title[0]]['children'] = array();
				}
			}
			else {
				$sorted_categories[] = $category_array;
			}
		}
		
		return $sorted_categories;
	}
	
	
	/**
	 * Displays a single category checkbox.
	 */
	public static function display_category_checkbox($category, $selected_categories, $form_field) {
		$html = '';
		
		$cid = $category['cid'];
		$category_title = $category['category'][0];
		$category_color = $category['category'][1];
			
		// Category is selected.
		$category_checked = in_array($cid, $selected_categories);
		
		$html .= form::checkbox($form_field.'[]', $cid, $category_checked, ' class="check-box"');
		$html .= $category_title;
		
		return $html;
	}
	
	
	/**
	 * Display category tree with input checkboxes.
	 * The is called "pseudo_tree" because the tree is generated based on naming convention.
	 * For example "1. Category One", "1a. Category One A".
	 * This is a stopgap solution for Haiti instance.
	 * @todo: Category hierarchy should really be defined in the database.
	 */
	public static function pseudo_tree_checkboxes(array $categories, array $selected_categories, $form_field, $columns = 1) {
		$html = '';
		
		// Validate columns
		$columns = (int) $columns;
		if ($columns == 0) {
			$columns = 1;
		}
		
		$categories_total = count($categories);
		
		// Organize categories into a hierarchical array.
		$sorted_categories = category::generate_pseudo_tree($categories);
									
		// Format categories for column display.
		$this_col = 1; // column number
		$maxper_col = round(count($sorted_categories)/$columns); // Maximum number of elements per column
		$i = 1;  // Element Count	
		foreach ($sorted_categories as $category) {
			
			// If this is the first element of a column, start a new UL
			if ($i == 1) {
				$html .= '<ul id="category-column-'.$this_col.'">';
			}
			
			// Display parent category.
			$html .= '<li>';
			$html .= category::display_category_checkbox($category, $selected_categories, $form_field);
			
			// Display child categories.
			if (!empty($category['children']) && is_array($category['children'])) {
				$html .= '<ul>';
				foreach ($category['children'] as $child_category) {
					$html .= '<li>';
					$html .= category::display_category_checkbox($child_category, $selected_categories, $form_field);
				}
				$html .= '</ul>';
			}
			$i++;
			
			// If this is the last element of a column, close the UL
			if ($i > $maxper_col || $i == $categories_total) {
				$html .= '</ul>';
				$i = 1;
				$this_col++;
			}
		}
		
		return $html;
	}	
}
