<?php
add_action('admin_menu', 'pl_Add_Admin_Link');

function pl_Add_Admin_Link()
{
    add_menu_page(
        'Post Link Search',
        'Post Link Search',
        'manage_options',
        PL_DIR . 'includes/pl-main-page.php'
    );

    add_submenu_page(
        PL_DIR . 'includes/pl-main-page.php',
        'Opciones de los Posts',
        'Opciones de los Posts',
        'manage_options',
        PL_DIR . 'includes/pl-main-page.php'
    );
    
    add_submenu_page(
        PL_DIR . 'includes/pl-main-page.php',
        'Crear Buscadores',
        'Crear Buscadores',
        'manage_options',
        PL_DIR . 'includes/pl-engines-menu.php'
    );

    remove_submenu_page(PL_DIR . 'includes/pl-main-page.php', PL_DIR . 'includes/pl-main-page.php');
}