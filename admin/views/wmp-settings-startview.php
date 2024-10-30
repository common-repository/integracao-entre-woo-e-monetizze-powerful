<div class="wrap metabox-holder woomonetizze">
	<h2>Integração entre Monetizze e Woocommerce</h2>
	<div class="row postbox">
		<br style="clear: both;">
		<form action="#"  id="wmp-form-options" method="post">
			<div class="col m3 s12">
				<p><b>Opções</b> :</p>
			</div>
			<div class="col m7 s12">
				<div>
					<label>Função para usuarios com pedidos/assinaturas aprovados: 
						
					<select name="funcao-aprovado">
						<?php foreach ($editable_roles as $k => $roles): ?>
						<option value="<?php echo esc_attr($k); ?>" <?php echo $opcoes['funcao-aprovado']==$k ? 'selected' : ''; ?> ><?php echo esc_attr($roles['name']); ?></option>
						<?php endforeach ?>
					</select>
					</label>
				</div>
				<div>
					<label>Função para usuarios com pedidos/assinaturas cancelados ou reembolsados: 
					<select name="funcao-reprovado" id="">
						<?php foreach ($editable_roles as $k => $roles): ?>
						<option value="<?php echo esc_attr($k); ?>" <?php echo $opcoes['funcao-reprovado']==$k ? 'selected' : ''; ?>><?php echo esc_attr($roles['name']); ?></option>
						<?php endforeach ?>
					</select>
					</label>
				</div>
			</div>
			<div class="col m2 s12">
				<p id="optionsInput"><button class="button button-primary" type="submit" value="opcoes">Salvar Opções</button></p>
			</div>
			<input id="_wpnonce" name="_wpnonce" type="hidden" value="<?php echo wp_create_nonce('wmp_opcoes'); ?>">
		</form>
		<div class="col m3 s12">
			<p>URL de Webhook :</p>
		</div>
		<div class="col m9 s12">
			<input value="<?php echo get_site_url(); ?>/?woomonetizzepowers=1" size="50" type="text" disabled="disabled"><a style="text-decoration: none;" target="_blank" href="https://app.monetizze.com.br/ferramentas/postback"><span class="dashicons dashicons-editor-help"></span></a>
		</div>
		<br style="clear: both;">
	</div>
	<div id="painel" class="col s12">
		<h2>Pedidos Importados</h2>
		<p>Lista dos ultimos pedidos importados </p>
		<table id="wmp-tabela-acoes" class="widefat fixed striped margin-top-bottom15">
			<thead>
				<tr>
					<th>#</th>
					<th>Cliente</th>
					<th>Data</th>
					<th>Total</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($ultimasVendas as $vendas): ?>
				<tr>
					<td><?php echo esc_attr($vendas->get_id()); ?></td>
					<td>
						<?php echo esc_attr($vendas->get_billing_first_name()); ?> <br />
						<?php echo $vendas->get_billing_email(); ?>
					</td>
					<td><?php echo date("d/m/Y" , strtotime($vendas->get_date_created())); ?></td>
					<td><?php echo 'R$ '.$vendas->get_total(); ?></td>
					<td><?php echo $vendas->get_status(); ?></td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<?php if (empty($ultimasVendas)): ?>

			<p style="text-align: center;">Nenhum pedido encontrado.</p>

		<?php endif ?>

	</div>
	<div class="footer">
		<div class="wmp-updated"> 
	        <div class="notice-image">
	        	<a href="https://powerfulautochat.com.br/" target="_blank">
	            <img style="max-width: 90px;" src="https://ps.w.org/powers-triggers-of-woo-to-chat/assets/icon-128x128.png?rev=2460034" alt="Powerful Auto Chat" >
	        	</a>
	        </div>
	        <div class="notice-content" style="margin-left: 15px;">
	            <p>
	            	Já imaginou o seu cliente receber uma mensagem por <b>Whatsapp</b> assim que ele realizar o pedido? <br />
	            	Conheça essa e outras vantagens de automatizar o atendimento com o plugin <a href="https://powerfulautochat.com.br/" target="_blank"><b>Powerful Auto Chat</b></a>.
	            </p>
	        </div>
	    </div>
		<p>
			Encontrou algum bug ou quer fazer um comentário? <a href="https://wordpress.org/support/plugin/integracao-entre-monetizze-e-wc-powers/" target="_blank">Entre em contato aqui</a> ⭐⭐⭐⭐⭐ Gostou do plugin? Considere dar 5 estrelas em uma avaliação no <a href="https://wordpress.org/support/plugin/integracao-entre-monetizze-e-wc-powers/reviews/#new-post" target="_blank">wordpress.org</a>. Obrigado! :)
		</p>
		<p>Precisa de um desenvolvedor Wordpress para o seu negócio ? <a target="_blank" href="http://felipepeixoto.tecnologia.ws/">Entre em contato</a>.</p>
	</div>
	<input id="pluginurl" type="hidden" value="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>">
	<input id="ajaxurl" type="hidden" value="<?php echo admin_url('admin-ajax.php'); ?>">

</div>