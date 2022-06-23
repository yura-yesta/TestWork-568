<?php

class WC_TTO_Product
{

    public static function init()
    {

        add_action(
            'woocommerce_single_product_summary',
            array(self::class, 'wc_display_create_date'),
            8
        );
        add_action('woocommerce_single_product_summary',
            array(self::class, 'wc_display_type_product'),
            9
        );
        add_action(
            'add_meta_boxes',
            array(self::class, 'add_meta_box_custom'),
            50
        );
        add_action(
            'save_post',
            array(self::class, 'save_meta_box_custom'),
            10, 1
        );
        add_action(
            'post_edit_form_tag',
            array(self::class, 'post_edit_form_tag')
        );


        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    }

    public static function wc_display_create_date()
    {
        global $product;

        $start = date('n-j-Y', strtotime($product->get_date_created()));

        echo "<div class='date-custom'>Created: $start</div>";

    }


    public static function wc_display_type_product()
    {
        global $product;

        $type = get_post_meta($product->get_id(), 'select_type_product');

        echo "<div class='type-custom'>Type of product: $type[0]</div>";

    }


    public static function add_meta_box_custom()
    {
        add_meta_box(
            'custom_product_meta_box',
            __('Additional custom fields', 'cmb'),
            array(self::class, 'add_custom_content_meta_box'),
            'product',
            'side',
            'default',
        );
    }


    public static function add_custom_content_meta_box($post)
    {
        $date = sprintf(
            '%1$s at %2$s',
            date("M j, Y", strtotime($post->post_date)),
            date("H:i", strtotime($post->post_date)),
        );
        $thumbnail_id = get_post_meta($post->ID, '_thumbnail_id');
        $image = wp_get_attachment_image_url($thumbnail_id[0]?? '', 'thumbnail');

        ?>
        <img class="form_cust" id="cust_img" src="<?= $image ?>" alt="" width="256">
        <div id="select-custom">
            <label for="myfile">Select a file:</label>
            <input type="file" id="myfile" name="wp_custom_attachment" class="form_cust" onchange="readFile(this)">
            <input type="hidden" id="delete_img" name="delete_img" value="">
        </div>
        <p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="event.preventDefault(); clear_img()">Delete
                image</a></p>
        <div class="misc-pub-section curtime misc-pub-curtime">
    				<span id="timestamp">
    					Published on: <b><?= $date ?></b>
    				</span>
        </div>
        <div class="" id="catalog-visibility">
            <?php

            woocommerce_wp_select(array(
                'id' => 'select_type_product',
                'class' => 'form_cust',
                'label' => 'Product type',
                'description' => 'Какие горы входят в этот фрирайд-трип?',
                'desc_tip' => true,
                'style' => 'margin-bottom:0px;',
                'value' => get_post_meta(get_the_ID(), 'select_type_product', true),
                'options' => array(
                    '' => 'Select...',
                    'rare' => 'rare',
                    'frequent' => 'frequent',
                    'unusual' => 'unusual'
                )
            ));

            ?>
        </div>
        <div>
            <div id="delete-action">
                <input type="button" class="button " value="clear" onclick="clear_all()">
            </div>
            <div id="publishing-action">
                <span class="spinner"></span>
                <input name="original_publish" type="hidden" id="original_publish" value="Update">
                <input type="submit" name="save" id="publish" class="button button-primary button-large" value="Update">
            </div>
            <div class="clear"></div>
        </div>
        <?php

        add_action('admin_footer', array(self::class, 'add_custom_script'));

    }


    public static function save_meta_box_custom($post_id)
    {

        update_post_meta($post_id, 'select_type_product', wp_kses_post($_POST['select_type_product']));


        if (!empty($_FILES['wp_custom_attachment']['name'])) {

            $supported_types = array('image/png', 'image/jpeg');
            $arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
            $uploaded_type = $arr_file_type['type'];

            if (in_array($uploaded_type, $supported_types)) {

                $attachment_id = self::save_file_image();
                update_post_meta($post_id, '_thumbnail_id', $attachment_id);

            } else {

                wp_die("Error... Try again!");
            }
        } else {

            if ($_POST['delete_img']) {

                update_post_meta($post_id, '_thumbnail_id', false);
            }
        }

    }


    public static function post_edit_form_tag()
    {
        echo ' enctype="multipart/form-data"';
    }


    public static function add_custom_script()
    {
        wp_enqueue_script('newscript', get_template_directory_uri() . '/woocommerce/js/custom_script.js');
    }

    public static function save_file_image()
    {

        $upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null,
            file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));


        if (isset($upload['error']) && $upload['error'] != 0) {
            wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
        } else {
            $attachment = array(
                'guid' => $upload['file'],
                'post_mime_type' => 'image/png',
                'post_title' => trim(strtolower('$name')) ?? 'qwerty',
                'post_name' => trim(strtolower('$name')) ?? 'qwerty',
                'post_date' => date('Y-m-d H:i:s'),
                'post_content' => '',
                'post_status' => 'inherit',
                'post_type' => 'attachment',
            );

            $attachment_id = wp_insert_attachment($attachment, $upload['file']);

            wp_generate_attachment_metadata($attachment_id, $upload['file']);

            return $attachment_id;

        }

    }

}