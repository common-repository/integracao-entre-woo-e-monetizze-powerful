<?php
class WooMonetizzepowers_Admin {


	function __construct() {

		//ajax
		add_action( 'wp_ajax_wmp_opcoes', array( $this, 'wmp_opcoes' ) );
		add_action( 'admin_menu', array( $this, 'wmp_settings_add_menu' ) );
		
	}

	function wmp_settings_add_menu (){
		add_menu_page( 'Woo & Monetizze Powers', 'Pedidos Monetizze', 'manage_options', 'woomonetizzepowers', array( $this, 'wmp_settings_startview' ), 'dashicons-download');		
	}

	function wmp_erro_view (){
?>
	    <div class="notice notice-error is-dismissible">
	    	<h3>Woocommerce não encontrado</h3>
	    	<p>Para utilizar este plugin é necessário que o woocommerce esteja instalado.</p>
	    </div>
<?php
	}

	function wmp_settings_startview (){
		if (!is_plugin_active('woocommerce/woocommerce.php')){
			$this->wmp_erro_view();
			return false;
		}

		wp_enqueue_script(  'wmp-script', plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery'), rand(0,1000), true );
		wp_enqueue_style( 'wmp-style', plugin_dir_url( __FILE__ ) . 'css/style.css', '', rand(0,1000), false );
		$opcoes = get_option( 'wmp_options', '' );
		if (empty($opcoes)) {
			$opcoes = array();	
		}

		global $wp_roles;
   		$all_roles = $wp_roles->roles;
    	$editable_roles = apply_filters('editable_roles', $all_roles);
		$opcoes['funcao-aprovado'] = !isset($opcoes['funcao-aprovado']) ? 'customer' : $opcoes['funcao-aprovado'];
		$opcoes['funcao-reprovado'] = !isset($opcoes['funcao-reprovado']) ? 'subscriber' : $opcoes['funcao-reprovado']; 

		//Montagem da lista
		$orders = wc_get_orders(array(
			'limit' => 20,
			'orderby' => 'date',
    		'order' => 'DESC',
		    'type'=> 'shop_order',
		    'meta_key' => 'wmp_monetizze_saleid',
		    'meta_compare' => '!=',
		    'meta_value' =>  ''
	    ));
	    $ultimasVendas = $orders;
		
		require_once plugin_dir_path(dirname(__FILE__)).'admin/views/wmp-settings-startview.php';

	}


	function wmp_opcoes (){

		if (!current_user_can('manage_options') or !isset($_POST['dados'])) {
			exit('false');
		}
		$dados = array();
		parse_str($_POST['dados'],$dados);
		foreach ($dados as $key => $value) {
			$dados[$key] = sanitize_text_field( trim($value) );
		}			
		
		if (!wp_verify_nonce($dados['_wpnonce'],'wmp_opcoes')) {
			exit('false');
		}
		unset($dados['_wpnonce']);
		$up = update_option('wmp_options', $dados,FALSE);
		if (!$up) {
			$up = add_option('wmp_options', $dados);
		}
		exit('true');
	}
}