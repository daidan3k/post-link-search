<?php
require_once(PL_DIR . 'includes/data-tables/pl-engines-data-table.php');

function showDataTable()
{
    $taula = new DataTable();

    $taula->set_table_name('pl_engines');
    $taula->set_columns(
        array(
            'engine' => "Buscador",
            'link' => "Enlace"
        )
    );

    $taula->set_sortable_columns(
        array(
            'engine' => array('post_title', false),
            'link' => array('status', false)
        )
    );

    $taula->set_column_name_links('engine');
    $message = "";
    if (processar_canvi_estat($taula)) {
        $message = ($_REQUEST['action'] == "delete" & $_SERVER['REQUEST_METHOD'] != 'POST') ?
            "<div class='error below-h2' id='message'>
             <p>El buscador se ha eliminado.</p></div>" :
            "<div class='updated below-h2' id='message'>
             <p>El buscador se ha añadido</p></div>";
    }
    $taula->prepare_items();
    echo $message;
    $taula->display();
}

function processar_canvi_estat($taula)
{
    if ('delete' === $taula->current_action()) {
        global $wpdb;
        $sql = "DELETE FROM pl_engines WHERE ID = " . $_REQUEST['engineID'];
        $wpdb->query($sql);
        return true;
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $wpdb;
        $sql = "INSERT INTO pl_engines (engine, link) 
                VALUES ('" . $_POST['engine'] . "', '" . $_POST['link'] . "')";

        $wpdb->query($sql);
        return true;
    }
    return false;
}

?>

<h1>Administrar buscadores</h1>
<div class="wrap">
    <?php
    showDataTable();
    ?>
    <h2>Añade nuevos buscadores</h2>
    <form method="post" action="" class="wp-admin">
        <label for="engine">Nombre del buscador:</label>
        <input type="text" name="engine" id="engine" class="regular-text" required><br><br>
        <label for="link">Link:</label>
        <input type="text" name="link" id="link" class="regular-text" required><br><br>
        <input type="submit" name="submit" value="Añadir" class="button button-primary">
    </form>
</div>

<style>
    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
    }
</style>