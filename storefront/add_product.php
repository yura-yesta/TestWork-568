<?php
/*
Template Name: Create product
*/
get_header();
?>
    <div id="notification_message"></div>
    <form id="productForm" method="POST">
        <img class="form_cust" id="cust_img" src="" alt="" width="256">
        <div class="input-group mb-3">
            <input type="file" class="form-control" id="myfile" name="wp_custom_attachment" onchange="readFile(this)">
            <label class="input-group-text" for="myfile">Upload</label>
        </div>
        <p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="event.preventDefault(); clear_img()">Delete
                image</a>
        </p>
        <div class="mb-3">
            <label for="exampleInput" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="exampleInput" name="product_name">
        </div>
        <div class="mb-3">
            <label for="exampleInputPrice" class="form-label">Price</label>
            <input type="text" class="form-control" id="exampleInputPrice" name="product_price">
        </div>
        <div class="mb-3">
            <label for="exampleType" class="form-label">Product type</label>
            <select id="exampleType" class="form-select" name="product_type">
                <option value="rare">Rare</option>
                <option value="frequent">Frequent</option>
                <option value="unusual">Unusual</option>
            </select>
        </div>
        <input type="hidden" name="action" value="add_product_ajax">
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <script>
        jQuery(function ($) {

            $('#productForm').submit(function (event) {
                event.preventDefault()
                $('#notification_message').text('');

                let fd = new FormData(this);

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php") ?>',
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#notification_message').text(data);
                        $('#productForm')[0].reset();
                        document.getElementById('cust_img').src = ''
                    }
                });
            });

        });

    </script>
<?php
get_footer();
