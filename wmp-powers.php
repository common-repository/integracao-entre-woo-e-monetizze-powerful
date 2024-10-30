<?php 
/**
 * Plugin Name: Integração entre Woo e Monetizze - Powerful
 * Plugin URI:	
 * Description:	Interação entre Woocommerce e Monetizze para download de pedidos via webhook.
 * Version:		1.1.0
 * Author:		Felipe Peixoto
 * Author URI:	http://felipepeixoto.tecnologia.ws/
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
$wmp_path = plugin_dir_path(__FILE__);
if (is_admin()){
	require plugin_dir_path( __FILE__ ) . 'admin/class-wmp-admin.php';
	$settings = new WooMonetizzepowers_Admin();
}

if (isset($_GET['woomonetizzepowers']) and isset($_POST)) {
	add_action( 'init', 'wmp_action_monetizze_webhook', 10, 0 ); 
}
function wmp_action_monetizze_webhook () {
	if (empty($_POST)) {
		exit('dados vazio.');
	}
	$wmp_path = plugin_dir_path(__FILE__);
	include_once($wmp_path . "include/class-wmp-webhook.php");
	$monetizze = new wooMonetizzepowers_Webhook($_POST);
	exit();
}
register_activation_hook( __FILE__, 'wmp_active_plugin' );
function wmp_active_plugin() {
	add_action( 'admin_notices', function(){
		?>
	    <div class="notice notice-success is-dismissible">
	    	<p><b>Integração entre Monetizze e Woocommerce:</b></p>
	        <p>Obrigado por baixar e instalar este plugin! Precisa de um desenvolvedor Wordpress para o seu negócio ? <a target="_blank" href="http://felipepeixoto.tecnologia.ws/">Entre em contato</a>.</p>
	    </div>
	    <?php
	});
}


?>