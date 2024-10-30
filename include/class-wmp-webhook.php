<?php
class wooMonetizzepowers_Webhook{

	function __construct($dados) {
		$this->wmp_importa_venda($dados);
	}

	function wmp_importa_venda ($dados){
		
		if (!isset($dados) or empty($dados['venda'])) { 
			exit('false');
		}


		foreach ($dados as $key => $value)	{
			if (is_array($value)) {
				$$key =  $value ;
			} else {
				$$key = sanitize_text_field( trim($value) );
			}
		}

		if (isset($_GET['debug'])) {
			$comprador['email'] = rand(0,1000).$comprador['email'];
		}

		$opcoes = get_option( 'wmp_options', array() );
		if (!empty($opcoes) and !is_array($opcoes)) {
			$opcoes = json_decode($opcoes,true);
		}
		$opcoes['funcao-aprovado'] = !isset($opcoes['funcao-aprovado']) ? 'customer' : $opcoes['funcao-aprovado'];
		$opcoes['funcao-reprovado'] = !isset($opcoes['funcao-reprovado']) ? 'subscriber' : $opcoes['funcao-reprovado'];

		$clienteNome  = $comprador['nome'];
	    $clienteNome = explode(' ', $clienteNome);
	    $primeiroNome = $clienteNome[0];
	    unset($clienteNome[0]);
	    $sobrenome = implode(' ', $clienteNome);


	    if (isset($comprador)) {
	     	$doc_type = strlen($comprador['cnpj_cpf'])<16 ? 'cpf' : 'cnpj';
	     } else {
	     	$doc_type = 'cpf';
	     }

		$address = array(
		  'first_name' => $primeiroNome,
		  'last_name'  => $sobrenome,
		  'company'    => '',
		  'email'      => $comprador['email'],
		  'phone'      => $comprador['telefone'],
		  'address_1'  => $comprador['endereco'].' '.$comprador['numero'],
		  'address_2'  => $comprador['complemento'],
		  'city'       => $comprador['cidade'],
		  'state'      => $comprador['estado'],
		  'postcode'   => $comprador['cep'],
		  'country'    => $comprador['pais'],
		  $doc_type    => $comprador['cnpj_cpf']
		);


		if (isset($comprador['cnpj_cpf'])) {
			if (strlen($comprador['cnpj_cpf']) <= 11 or strlen($comprador['cnpj_cpf']) == 14 ) {
				$address['persontype'] = 1;
				$address['cpf'] = $comprador['cnpj_cpf'];
			}else{
				$address['persontype'] = 2;
				$address['cnpj'] = $comprador['cnpj_cpf'];
			}
		}


		//Adicionar usuario
	    $user = get_user_by( 'email', $comprador['email'] ); 
  		if ($user!=false) {
  			$address['first_name'] = $user->first_name;
  			$address['last_name'] = $user->last_name;
  			$user_id = $user->ID;
  		} else{
  			$random_password = wp_generate_password( 8, false );
  			$user_name = explode('@', $comprador['email']);
  			$user_name = $user_name[0].date("is");
  			$user_id = wp_create_user( $user_name, $random_password, $comprador['email'] );
  			if( !is_wp_error($user_id) ) {
  				$user = get_user_by( 'id', $user_id );
	  			wp_update_user([
				    'ID' => $user_id, // this is the ID of the user you want to update.
				    'first_name' => $primeiroNome,
				    'last_name' => $sobrenome,
				]);
				wp_new_user_notification($user_id, null, 'user');
  			}	
  		}

  		$codUnico = sha1($venda['dataInicio'].$comprador['email']);
		$orders = wc_get_orders(array(
		    'type'=> 'shop_order',
		    'orderby' => 'date',
            'order'   => 'DESC',
		    'meta_key' => 'wmp_monetizze_saleid',
		    'meta_compare' => '==',
		    'meta_value' =>  $codUnico
	    ));
	    if (!empty($orders)) {
	    	foreach ($orders as $order) {
		    	switch ($venda['status']) {
		  			case 'Finalizada':
		  				$order->update_status("processing", 'Status alterado pelo Monetizze', TRUE);
		  				$user->add_role( $opcoes['funcao-aprovado'] );
		  				break;
		  			case 'Completa':
		  				$order->update_status("completed", 'Status alterado pelo Monetizze', TRUE);
		  				$user->add_role( $opcoes['funcao-aprovado'] );
		  				break;
		  			case 'Cancelada':
		  				$order->update_status("cancelled", 'Status alterado pelo Monetizze', TRUE);
		  				$user->add_role( $opcoes['funcao-reprovado'] );
		  				break;
		  			default:
		  				$order->update_status("pending", 'Status alterado pelo Monetizze', TRUE);
		  				break;
		  		}
		  		$order->set_customer_id( $user_id );
  				$order->set_address( $address, 'billing' );
		  		$up = update_post_meta($order->get_id(), 'wmp_monetizze_dados', $dados);
		  		$up = update_post_meta($order->get_id(), 'wmp_monetizze_saleid', $codUnico);
		  	}
	    	return; 
	    }


	    $order = wc_create_order();
		$newOrderId = $order->get_id();
		
		$order->set_customer_id( $user_id );
  		$order->set_address( $address, 'billing' );
  		

  		//Produtos
		if (!empty($produto)) {
			$args = array(
				'post_type' => 'product',
				'meta_key' => 'wmp_monetizze_id',
				'meta_value' => $produto['codigo'],
			);

			$produtos = get_posts($args);
			foreach ($produtos as $p) {
				$order->add_product( get_product($p->ID), 1);
			}

  		}

  		$order->calculate_totals();

  		$up = update_post_meta($newOrderId, 'wmp_monetizze_saleid', $codUnico);
		if (!$up) {
			$up = add_post_meta($newOrderId, 'wmp_monetizze_saleid', $codUnico);
		}
		$up = update_post_meta($newOrderId, 'wmp_monetizze_dados', $dados);

  		do_action( 'woocommerce_checkout_order_processed', $newOrderId, $dados, $order ); 
		do_action( 'wmp_order_created', $order, $newOrderId ); 

		foreach ($user->roles as $role) {
			$user->remove_role( $role );
		}
  		switch ($venda['status']) {
  			case 'Finalizada':
  				$order->update_status("processing", 'Status alterado pelo Monetizze', TRUE);
  				$user->add_role( $opcoes['funcao-aprovado'] );
  				break;
  			case 'Completa':
  				$order->update_status("completed", 'Status alterado pelo Monetizze', TRUE);
  				$user->add_role( $opcoes['funcao-aprovado'] );
  				break;
  			case 'Cancelada':
  				$order->update_status("cancelled", 'Status alterado pelo Monetizze', TRUE);
  				$user->add_role( $opcoes['funcao-reprovado'] );
  				break;
  			default:
  				$order->update_status("pending", 'Status alterado pelo Monetizze', TRUE);
  				break;
  		}
	}
}