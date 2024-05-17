<?php
add_action('the_post', 'search_in_google');

function search_in_google()
{
    global $wpdb;    
    $engine = get_post_meta(get_the_id(), '_pl_search_engine', true);  
    $activated = get_post_meta(get_the_id(), '_pl_search', true);
    $keyword = get_post_meta(get_the_id(), '_pl_search_keyword', true);

    $results = $wpdb->get_results("SELECT * FROM pl_engines WHERE engine = '$engine'", ARRAY_A);
    if ($results != null) {
        $link = $results[0]['link'];
    } else {
        $link = "http://google.es/search?q=";
    }
    if ($activated == "true" & !is_admin()) {
        
       /* switch ($engine) {
            default:
            case 'google':
                $link = "http://google.es/search?q=";
            break;
            case 'eventbrite':
                $link = "https://www.eventbrite.com/d/spain/";
                break;
            case 'tripadvisor':
                $link = "https://www.tripadvisor.es/Search?q=";
                break;
            case 'amazon':
                $link = "https://www.amazon.es/s?k=";
                break;
        }*/
        ?>
        <script type="text/javascript">
            function open_tab() {
                window.open("<?php echo $link . $keyword; ?>", '_blank').focus();
            }
            window.onload = open_tab();
        </script>
        <?php
    }
}


function import_libs()
{
    wp_register_script('search-engine-script', plugins_url('pl-search/js/search-engine.js'), array(), '1.0', true);
    wp_enqueue_script('search-engine-script');
    wp_register_script('keyword-script', plugins_url('pl-search/js/keyword.js'), array(), '1.0', true);
    wp_enqueue_script('keyword-script');
}
add_action('admin_enqueue_scripts', 'import_libs');
add_action('wp_ajax_update_metadata', 'update_metadata_callback');

function update_metadata_callback()
{
    global $wpdb;

    $metakey = $_POST['metakey'];
    $metavalue = $_POST['metavalue'];
    $id = $_POST['row_id'];

    if ($wpdb->query("SELECT * FROM wp_postmeta WHERE post_id = $id AND meta_key = '$metakey'") == 0) {
        $sql = "INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
                VALUES ($id, '$metakey', '$metavalue')";
        $result = $wpdb->query($sql);
        if ($result !== false) {
            wp_send_json_success('Insert suceeded; option=' . $metavalue . '; id=' . $id . '; query=' . $sql);
        } else {
            wp_send_json_error('Insert failed; option=' . $metavalue . '; id=' . $id . '; query=' . $sql);
        }
    } else {
        $sql = "UPDATE wp_postmeta
                SET meta_value = '$metavalue'
                WHERE post_id = $id
                AND meta_key = '$metakey'";
        $result = $wpdb->query($sql);
        if ($result !== false & $result !== 0) {
            wp_send_json_success('Update succeeded; option=' . $metavalue . '; id=' . $id . '; query=' . $sql);
        } else {
            wp_send_json_error('Update failed; option=' . $metavalue . '; id=' . $id . '; query=' . $sql);
        }
    }
    wp_die();
}

?>