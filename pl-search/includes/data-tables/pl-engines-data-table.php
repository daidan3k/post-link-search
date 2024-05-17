<?php
// Carregar el fitxer de la classe WP_List_Table (nucli del WordPress)
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Classe que hereda de WP_List_Table
 * 
 * Afegeix atributs i mètodes a la classe base per adapart-ho a les necessitats pròpies.
 */
class DataTable extends WP_List_Table
{
    /**
     * Atributs de la classe
     */
    var $table_data;        // referència a les dades que ha de mostrar la taula
    var $table_name;        // nom de la taula de la BD que es vol consultar
    var $columns;           // llista de columnes que mostrarà la taula
    var $sortable_columns;  // llista de columnes que es poden ordenar
    var $default_sort_column;   // columna amb què s'ordena per defecte
    var $column_links;      // columna que conté les opcions Edit i Remove      
    var $column_name_links; // String amb el codi HTML dels enllaços Edit i Remove
    var $edit_form;         // String amb el nom del formulari d'edició
    /**
     * Setter / Getter del nom de la taula
     */
    function set_table_name($name)
    {
        $this->table_name = $name;
    }

    function get_table_name()
    {
        return $this->table_name;
    }
    /**
     * Setter / Getter del formulari d'edició
     */
    function set_edit_form($form_name)
    {
        $this->edit_form = $form_name;
    }

    function get_edit_form()
    {
        return $this->edit_form;
    }


    /**
     * Setter / Getter de la llista de columnes
     */
    function set_columns($columns)
    {
        $this->columns = $columns;
    }

    function get_columns()
    {
        //$this->columns["dropdown"] = "Buscador";
        return $this->columns;
    }

    /**
     * Setter amb la columna que conté els enllaços Edit/Remove
     */
    function set_column_links($value)
    {
        $this->column_links = $value;
    }
    /**
     * Setter amb el codi HTML que  conté els enllaços Edit/Remove
     */
    function set_column_name_links($value)
    {
        $this->column_name_links = $value;
    }

    /**
     * Mètode sobreescrit que obté les dades i configura  la taula
     */
    function prepare_items()
    {
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $per_page = $this->get_items_per_page('elements_per_page', 10);
        // Si hi ha cadena de cerca (filtre)
        if (isset($_POST['s'])) {
            $this->table_data = $this->get_table_data($per_page, $paged, $_POST['s']);
        } else {
            $this->table_data = $this->get_table_data($per_page, $paged);
        }
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $primary = $this->default_sort_column;
        $hidden = array();
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);
        // Agafar les dades que toquen per la pàgina actual
        //$found_data = array_slice($dades, (($current_page - 1) * $per_page), $per_page);
        //$this->found_data = $this->rows;
        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page' => $per_page,
                'total_pages' => ceil($total_items / $per_page)
            )
        );

        $this->items = $this->table_data;
    }

    /**
     * Mètode sobreescrit per retornar el text a mostrar per defecte de cada  columna 
     * i de la columna que ha de contindre els enllaços Edit / Remove
     */
    function column_default($item, $column_name)
    {
        if (isset($column_name) && $column_name == $this->column_name_links) {
            $actions = array(
                'delete' => sprintf(
                    '<a href="?page=%s&action=%s&engineID=%s">Eliminar</a>',
                    $_REQUEST['page'],
                    'delete',
                    $item['ID']
                ),                
            );
            return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions));
        } else {
            return $item[$column_name];
        }
    }

    /**
     * Getter / Setter de les columnes ordenables
     */
    function get_sortable_columns()
    {
        return $this->sortable_columns;
    }

    function set_sortable_columns($sortable_columns)
    {
        $this->sortable_columns = $sortable_columns;
    }

    /**
     * Setter / Getter de la columna per la qual s'ordena per defecte
     */
    function set_default_sort_column($column_name)
    {
        $this->default_sort_column = $column_name;
    }

    function get_default_sort_column()
    {
        return !empty($this->default_sort_column) ? $this->default_sort_column : "ID";
    }

    /** 
     * Mètode que s'invoca per ordenar el contingut d'una columna
     */
    function usort_reorder($a, $b)
    {
        // Si no s'indica columna, per defecte els cognoms
        //$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'cognoms';
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : $this->get_default_sort_column();
        // Si no hi ha ordre, per defecte asendent
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
        // Indica com s'ordena
        $result = strcmp($a[$orderby], $b[$orderby]);
        // Enviaa l'adreça d'ordenació al final de usort
        return ($order === 'asc') ? $result : -$result;
    }


    /**
     * Mètode per obtenir les dades la taula, amb un nombre de registres per pàgina i criteris de cerca
     */
    private function get_table_data($per_page, $paged, $search = '')
    {
        global $wpdb;

        if (!empty($search)) {
            return

                $wpdb->get_results(
                                    "SELECT *
                                     FROM $this->table_name",
                    ARRAY_A
                );
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT *
                                                    FROM $this->table_name",
                $per_page,
                $paged
            ), ARRAY_A);
        }
    }

    /**
     * Esborra un conjunt de files
     * 
     * @param $ids array d'ID que es volen eliminar
     */
    public function delete_data($ids)
    {
        global $wpdb;

        if (!empty($ids)) {
            $str = "DELETE FROM $this->table_name WHERE id IN($ids)";
            return $wpdb->query($str);
        }
        return false;
    }
}
