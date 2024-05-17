<?php
require_once(PL_DIR . 'includes/data-tables/pl-main-data-table.php');

function showDataTable()
{
    $taula = new DataTable();

    $taula->set_table_name('wp_posts');
    $taula->set_columns(
        array(
            'status' => "Estado",
            'post_title' => "Titulo del post",
            'dropdown' => "Buscador",
            'textinput' => "Palabra clave"
        )
    );

    $taula->set_sortable_columns(
        array(
            'post_title' => array('post_title', false),
            'status' => array('status', false)
        )
    );

    $taula->set_column_name_links('status');
    $message = "";
    if (processar_canvi_estat($taula)) {
        $message = ($_REQUEST['action'] == "activar") ?
            "<div class='updated below-h2' id='message'>
             <p>La busqueda en Google del post esta activada</p></div>" :
            "<div class='error below-h2' id='message'>
             <p>La busqueda en Google del post esta desactivada</p></div>";
    }
    $taula->prepare_items();
    echo $message;
    $taula->display();
    
    echo '<style type="text/css">';    
    echo '#status { width: 15%; }';
    echo '#post_title { width: 40%; }';
    echo '#dropdown { width: 10%; }';
    echo '#textinput { width: 35%; }';
    echo 'input[name="textinput"] { width: 90%; }';
    echo '</style>';
}

function processar_canvi_estat($taula)
{
    if ('activar' === $taula->current_action()) {
        return modificar_metadades('true');
    } else if ('desactivar' === $taula->current_action()) {
        return modificar_metadades('false');
    }
    return false;
}

function modificar_metadades($value)
{
    $id = isset($_REQUEST['wp_posts']) ? $_REQUEST['wp_posts'] : "";
    global $wpdb;
    if (!empty($id)) {
        // Si encara no existeix la entrada a wp_postmeta
        if ($wpdb->query("SELECT * FROM wp_postmeta WHERE post_id = '$id' AND meta_key = '_pl_search'") == 0) {
            $sql = "INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
                    VALUES ('$id', '_pl_search', '$value')";
            return $wpdb->query($sql);
        } else {
            $sql = "UPDATE wp_postmeta
                    SET meta_value = '$value'
                    WHERE post_id = '$id' AND meta_key = '_pl_search'";
            return $wpdb->query($sql);
        }
    }
    return false;
}
?>

<div class="wrap">
    <h1>Post Link Search Options</h1>
    <p>Panel para administrar que posts abriran una nueva pestaña de google relacionada con el articulo.</p>
    <?php
    showDataTable();
    ?>
</div>