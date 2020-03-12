<?php

/*
 * Plugin Name: EZ WooSeoCat
 * Plugin URI: https://zev-s.com/kod/wordpress/ez-wp-woo-seocat/
 * Description: Добавляем теги к категориям товаров woocommerce
 * Version: 0.0.1
 * Author: Evgenii Z
 * Author URI: https://zabairachnyi.com
 * License: GPLv2 or later
 
 */
 
 // Добавляем СЕО поля к рубрикам товаров WooCommerce

add_action("edit_category_form", 'ezseocat_category_meta');
add_action("product_cat_edit_form_fields", 'ezseocat_category_meta');

function ezseocat_category_meta($term) {
    ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label>Заголовок (title)</label></th>
            <td>
                <input type="text" name="ezseocat[mytitle]" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'mytitle', 1 ) ) ?>"><br />
                <p class="description">Не более 60 знаков, включая пробелы</p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label>Заголовок h1</label></th>
            <td>
                <input type="text" name="ezseocat[h1]" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'h1', 1 ) ) ?>"><br />
                <p class="description">Заголовок страницы</p>
            </td>
        </tr>
       <tr class="form-field">
<th scope="row" valign="top"><label>Краткое описание (description)</label></th>
<td>
<input type="text" name="ezseocat[description]" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'description', 1 ) ) ?>"><br />
<p class="description">Краткое описание (description)</p>
</td>
</tr>

        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label>Ключевые слова</label></th>
            <td>
                <input type="text" name="ezseocat[keywords]" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'keywords', 1 ) ) ?>"><br />
                <p class="description">Ключевые слова (keywords)</p>
            </td>
        </tr>
    <?php
}

function ezseocat_save_meta( $term_id ) {
    if ( ! isset($_POST['ezseocat']) )
        return;
    $ezseocat = array_map('trim', $_POST['ezseocat']);
    foreach( $ezseocat as $key => $value ){
        if( empty($value) ){
            delete_term_meta( $term_id, $key );
            continue;
        }
        update_term_meta( $term_id, $key, $value );
    }
    return $term_id;
}



add_action("create_product_cat", 'ezseocat_save_meta');
add_action("edited_product_cat", 'ezseocat_save_meta');
add_action("create_category", 'ezseocat_save_meta');
add_action("edited_category", 'ezseocat_save_meta');

function ezseocat_remove_seo_description( $data ){
return false;
}
if(strpos($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], '/catalog/')) {
add_filter( 'aioseop_title', 'ezseocat_remove_seo_description' );
}

/* Вывод title для категорий товаров */
add_filter('single_term_title', 'ezseocat_filter_single_cat_title', 10, 1);
add_filter( 'single_term_title', 'ezseocat_poduct_cat_title', 10, 1);


//add_filter('category_title', 'ezseocat_filter_single_cat_title', 10, 1);
//add_filter( 'category_title', 'ezseocat_poduct_cat_title', 10, 1);


function ezseocat_filter_single_cat_title() {
    $pci =  get_queried_object()->term_id;
    return get_term_meta ($pci, 'mytitle', true);
}
function ezseocat_poduct_cat_title($pct){
    if(empty($pct)){
        $pct = get_queried_object()->name;
    }
    return $pct;
}


function my_product_title($title){
if (is_product()){
    return 'Купить '.$title.' в {{region_to_city}}';

}
    }
function my_product_desc($description){
if (is_product()){

 global $post, $wp_query;
	$postID = $wp_query->post->ID;
	$product = wc_get_product( $postID );
	
    return 'Приобретайте '.get_the_title().' в {{region_to_city}} от компании «ПОЖЭКСПЕРТ» &#10143; Стоимость: '.$product->get_price().'&#8381; &#9654; Звоните: &#128383; {{region_phone}}. ';

} else return $description;
    }
    
//add_filter ('aioseop_title', 'my_product_title', 1);
//add_filter ('aioseop_description', 'my_product_desc', 1);



// Выводим СЕО Н1 в категории товаров
if(strpos($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], '/catalog/')) {
add_filter ( 'woocommerce_show_page_title' , 'ezseocat_woocommerce_product_cat_h1' , 10 , 2 );
}




function ezseocat_product_cat_h1(){
    $pch = get_term_meta (get_queried_object()->term_id, 'h1', true);
    echo '<h1 class="woocommerce-products-header__title page-title">'.replace_text($pch).'</h1>';
    if(empty($pch)){
       echo '<h1 class="woocommerce-products-header__title page-title">'.replace_text(get_queried_object()->name).'</h1>';
    }
}
function ezseocat_woocommerce_product_cat_h1(){
    return  ezseocat_product_cat_h1($pch);    
}




// Выводим СЕО  description в категории товаров
add_action('wp_head', 'ezseocat_description_product_cat', 1, 1);
function ezseocat_description_product_cat(){
    if( is_product_category() || is_product_tag() ){
    $pcd = get_term_meta (get_queried_object()->term_id, 'description', true);
    if(!empty($pcd)){
    $meta = '<meta name="description"  content="'.replace_text($pcd).'"/>'."\n";
    
    }
    else {        
       $pcd = wp_filter_nohtml_kses(substr(category_description(), 0, 280));
       $meta = '<meta name="description"  content="'.replace_text($pcd).'"/>'."\n";   
    }
    echo $meta;
    }
    
      if( is_category()){
    $pcd = get_term_meta (get_queried_object()->term_id, 'description', true);
    if(!empty($pcd)){
    $meta = '<meta name="description"  content="'.replace_text($pcd).'"/>'."\n";
    
    }
    else {        
       $pcd = wp_filter_nohtml_kses(substr(category_description(), 0, 280));
       $meta = '<meta name="description"  content="'.replace_text($pcd).'"/>'."\n";   
    }
    echo $meta;
    }
    
}

/* Вывод keywords для категорий товаров */
add_action('wp_head', 'ezseocat_keywords_product_cat', 1, 1);
function ezseocat_keywords_product_cat(){
    if(is_product_category()){
    $pck = get_term_meta (get_queried_object()->term_id, 'keywords', true );
    $aut = '<meta name="keywords" content="'.replace_text($pck).'">'."\n";
    }
   //  if(is_category()){
  //  $pck = get_term_meta (get_queried_object()->term_id, 'keywords', true );
   // $aut = '<meta name="keywords" content="'.replace_text($pck).'">'."\n";
    //}
    echo $aut;
}

/* Заголовок для /shop/
//add_filter ( 'woocommerce_show_page_title' , 'ezseocat_product_shop_h1' , 10 , 2 );
function ezseocat_product_shop_h1(){
$product = "Каталог товаров";
if(strpos($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], '/shop'))
echo '<h1 class="woocommerce-products-header__title page-title">' . $product . '</h1>';
}

*/


