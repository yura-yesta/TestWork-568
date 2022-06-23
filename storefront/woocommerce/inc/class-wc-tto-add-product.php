<?php

class WC_TTO_Add_Product
{

    public static function init()
    {

        add_action(
            'wp_ajax_add_product_ajax',
            array(self::class, 'add_product_ajax')
        );
        add_action(
            'wp_ajax_nopriv_add_product_ajax',
            array(self::class, 'add_product_ajax')
        );
        add_action(
            'wp_footer',
            array(WC_TTO_Product::class, 'add_custom_script')
        );

    }


    public static function add_product_ajax()
    {

       echo self::custom_create_product($_POST);

        die;
    }


    public static function custom_create_product($product)
    {
        if (empty($product['product_name'])) {

            return 'Empty name field...';
        }

        $post_id = self::create_post_id($product);

        if (empty($post_id)) {

            return 'Error';
        }

        if (!empty($_FILES['wp_custom_attachment']['name'])) {

            $thumbnail_id = WC_TTO_Product::save_file_image();
        }

        $args = array(
            '_visibility' => 'visible',
            '_stock_status' => 'instock',
            '_downloadable' => 'no',
            '_regular_price' => $product['product_price'] ?? 0,
            '_price' => $product['product_price'] ?? 0,
            'select_type_product' => $product['product_type'] ?? '',
            '_thumbnail_id' => $thumbnail_id ?? ''
        );

        foreach ($args as $key => $value) {

            update_post_meta($post_id, $key, $value);

        }

        return "Product $post_id created";

    }


    public static function create_post_id($file)
    {

        $args = array(
            'post_title' => $file['product_name'] ?? 'empty product_name',
            'post_excerpt' => '',
            'post_content' => '',
            'post_date' => date('Y-m-d H:i:s'),
            'post_author' => 1,
            'post_type' => 'product',
            'post_status' => 'publish',
        );

        return wp_insert_post($args);
    }


}
