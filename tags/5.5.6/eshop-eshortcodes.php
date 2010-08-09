<?php 
/* links into eShortcodes */
add_filter('eshortcodesType', 'eshorteShop');
function eshorteShop($values){
/*
format $values[type]='nice name for select box'
*/
	$values['eshop_list_alpha']=__('eShop Alpha','eshop');
	$values['eshop_list_subpages']=__('eShop Subpages','eshop');
	$values['eshop_list_cat_tags']=__('eShop Categories/Tags','eshop');
	$values['eshop_list_new']=__('eShop New','eshop');
	$values['eshop_best_sellers']=__('eShop Best Sellers','eshop');
	$values['eshop_list_featured']=__('eShop Featured','eshop');
	$values['eshop_list_random']=__('eShop Random','eshop');
	$values['eshop_show_product']=__('eShop Show Product','eshop');
	return $values;
}


add_filter('eshortcodesForm', 'eshorteShopForm');

function eshorteShopForm($data){
/*
id needs to match the type
all form id need to be of the format : yourType_shortcodeattribute
all form name need to be of the format : yourType[shortcodeattribute]
*/
?>
	<table id="eshop_list_alpha" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_list_alpha_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_list_alpha[class]" id="eshop_list_alpha_class" />
				<input type="hidden" name="eshop_list_alpha[content]" id="eshop_list_alpha_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_alpha_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_list_alpha_panels" name="eshop_list_alpha[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_alpha_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_list_alpha_form" name="eshop_list_alpha[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_alpha_records"><?php _e('Records:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_alpha[records]" id="eshop_list_alpha_records" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_alpha_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_alpha[imgsize]" id="eshop_list_alpha_imgsize" />
			</td>
		</tr>
	
		<tr>
			<th scope="row"><label for="eshop_list_alpha_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_list_alpha_links" name="eshop_list_alpha[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
	</table>
	<table id="eshop_list_subpages" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_list_subpages_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_list_subpages[class]" id="eshop_list_subpages_class" />
				<input type="hidden" name="eshop_list_subpages[content]" id="eshop_list_subpages_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_list_subpages_panels" name="eshop_list_subpages[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_list_subpages_form" name="eshop_list_subpages[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_show"><?php _e('Show:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_subpages[show]" id="eshop_list_subpages_show" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_records"><?php _e('Records:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_subpages[records]" id="eshop_list_subpages_records" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_sortby"><?php _e('Sort by:','eshop')?></label></th>
			<td><select id="eshop_list_subpages_sortby" name="eshop_list_subpages[sortby]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="post_date"><?php _e('Post Date','eshop')?></option>
			<option value="post_title"><?php _e('Post Title','eshop')?></option>
			<option value="menu_order"><?php _e('Menu Order','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_order"><?php _e('Order:','eshop')?></label></th>
			<td><select id="eshop_list_subpages_order" name="eshop_list_subpages[order]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="ASC"><?php _e('Ascending','eshop')?></option>
			<option value="DESC"><?php _e('Descending','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_id"><?php _e('Page id:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_subpages[id]" id="eshop_list_subpages_id" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_subpages_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_subpages[imgsize]" id="eshop_list_subpages_imgsize" />
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="eshop_list_subpages_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_list_subpages_links" name="eshop_list_subpages[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
	</table>
	<table id="eshop_list_cat_tags" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_list_cat_tags[class]" id="eshop_list_cat_tags_class" />
				<input type="hidden" name="eshop_list_cat_tags[content]" id="eshop_list_cat_tags_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_list_cat_tags_panels" name="eshop_list_cat_tags[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_list_cat_tags_form" name="eshop_list_cat_tags[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_show"><?php _e('Show:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_cat_tags[show]" id="eshop_list_cat_tags_show" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_records"><?php _e('Records:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_cat_tags[records]" id="eshop_list_cat_tags_records" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_sortby"><?php _e('Sort by:','eshop')?></label></th>
			<td><select id="eshop_list_cat_tags_sortby" name="eshop_list_cat_tags[sortby]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="post_date"><?php _e('Post Date','eshop')?></option>
			<option value="post_title"><?php _e('Post Title','eshop')?></option>
			<option value="menu_order"><?php _e('Menu Order','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_order"><?php _e('Order:','eshop')?></label></th>
			<td><select id="eshop_list_cat_tags_order" name="eshop_list_cat_tags[order]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="ASC"><?php _e('Ascending','eshop')?></option>
			<option value="DESC"><?php _e('Descending','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_cat_tags[imgsize]" id="eshop_list_cat_tags_imgsize" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_find"><?php _e('Find:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:95%;" name="eshop_list_cat_tags[find]" id="eshop_list_cat_tags_find" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_type"><?php _e('Type:','eshop')?></label></th>
			<td><select id="eshop_list_cat_tags_type" name="eshop_list_cat_tags[type]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="cat"><?php _e('category Id','eshop')?></option>
			<option value="category_name"><?php _e('Category name','eshop')?></option>
			<option value="tag"><?php _e('Tag','eshop')?></option>
			<option value="tag_id"><?php _e('Tag Id','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_cat_tags_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_list_cat_tags_links" name="eshop_list_cat_tags[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
	</table>
	<table id="eshop_list_new" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_list_new_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_list_new[class]" id="eshop_list_new_class" />
				<input type="hidden" name="eshop_list_new[content]" id="eshop_list_new_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_new_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_list_new_panels" name="eshop_list_new[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_new_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_list_new_form" name="eshop_list_new[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_new_show"><?php _e('Show:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_new[show]" id="eshop_list_new_show" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_new_records"><?php _e('Records:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_new[records]" id="eshop_list_new_records" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_new_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_new[imgsize]" id="eshop_list_new_imgsize" />
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="eshop_list_new_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_list_new_links" name="eshop_list_new[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
	</table>
	<table id="eshop_best_sellers" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_best_sellers_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_best_sellers[class]" id="eshop_best_sellers_class" />
				<input type="hidden" name="eshop_best_sellers[content]" id="eshop_best_sellers_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_best_sellers_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_best_sellers_panels" name="eshop_best_sellers[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_best_sellers_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_best_sellers_form" name="eshop_best_sellers[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_best_sellers_show"><?php _e('Show:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_best_sellers[show]" id="eshop_best_sellers_show" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_best_sellers_records"><?php _e('Records:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_best_sellers[records]" id="eshop_best_sellers_records" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_best_sellers_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_best_sellers[imgsize]" id="eshop_best_sellers_imgsize" />
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="eshop_best_sellers_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_best_sellers_links" name="eshop_best_sellers[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
	</table>
	<table id="eshop_list_featured" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_list_featured_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_list_featured[class]" id="eshop_list_featured_class" />
				<input type="hidden" name="eshop_list_featured[content]" id="eshop_list_featured_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_featured_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_list_featured_panels" name="eshop_list_featured[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_featured_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_list_featured_form" name="eshop_list_featured[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_featured_sortby"><?php _e('Sort by:','eshop')?></label></th>
			<td><select id="eshop_list_featured_sortby" name="eshop_list_featured[sortby]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="post_date"><?php _e('Post Date','eshop')?></option>
			<option value="post_title"><?php _e('Post Title','eshop')?></option>
			<option value="menu_order"><?php _e('Menu Order','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_featured_order"><?php _e('Order:','eshop')?></label></th>
			<td><select id="eshop_list_featured_order" name="eshop_list_featured[order]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="ASC"><?php _e('Ascending','eshop')?></option>
			<option value="DESC"><?php _e('Descending','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_featured_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_featured[imgsize]" id="eshop_list_featured_imgsize" />
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="eshop_list_featured_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_list_featured_links" name="eshop_list_featured[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
	</table>
	<table id="eshop_list_random" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_list_random_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_list_random[class]" id="eshop_list_random_class" />
				<input type="hidden" name="eshop_list_random[content]" id="eshop_list_random_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_list_random_panels" name="eshop_list_random[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_list_random_form" name="eshop_list_random[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_show"><?php _e('Show:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_random[show]" id="eshop_list_random_show" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_records"><?php _e('Records:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_random[records]" id="eshop_list_random_records" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_list_random[imgsize]" id="eshop_list_random_imgsize" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_list"><?php _e('List:','eshop')?></label></th>
			<td><select id="eshop_list_random_list" name="eshop_list_random[list]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_excludes"><?php _e('Exclude:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:95%;" name="eshop_list_random[excludes]" id="eshop_list_random_excludes" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_list_random_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_list_random_links" name="eshop_list_random[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
	</table>
	<table id="eshop_show_product" class="form-table">
		<tr>
			<th scope="row"><label for="eshop_show_product_class"><?php _e('Class:','eshop')?></label></th>
			<td>
				<input type="text" size="40" style="width:95%;" name="eshop_show_product[class]" id="eshop_show_product_class" />
				<input type="hidden" name="eshop_show_product[content]" id="eshop_show_product_content" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_show_product_panels"><?php _e('Panels:','eshop')?></label></th>
			<td><select id="eshop_show_product_panels" name="eshop_show_product[panels]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_show_product_form"><?php _e('Form:','eshop')?></label></th>
			<td><select id="eshop_show_product_form" name="eshop_show_product[form]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No','eshop')?></option>
			<option value="yes"><?php _e('Yes','eshop')?></option>
			</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_show_product_id"><?php _e('Page/Post Id:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_show_product[id]" id="eshop_show_product_id" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="eshop_show_product_imgsize"><?php _e('Image % size:','eshop')?></label></th>
			<td>
				<input type="text" size="8" style="width:30%;" name="eshop_show_product[imgsize]" id="eshop_show_product_imgsize" />
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="eshop_show_product_links"><?php _e('Links:','eshop')?></label></th>
			<td><select id="eshop_show_product_links" name="eshop_show_product[links]">
			<option value=""><?php _e('Default or choose','eshop')?></option>
			<option value="no"><?php _e('No')?></option>
			<option value="yes"><?php _e('Yes')?></option>
			</select>
			</td>
		</tr>
	</table>
<?php
}

/*

function eshop_show_product($atts){
	extract(shortcode_atts(array('id'=>'0','class'=>'eshopshowproduct','panels'=>'no','form'=>'no','imgsize'=>'','links'=>'yes'), $atts));
}
*/
?>