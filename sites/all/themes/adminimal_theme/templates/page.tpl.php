<?php
	global $base_url;
	$lib = $base_url."/".libraries_get_path('calendarUtil');
	$mask = $base_url."/".libraries_get_path('maskedinput') . '/src/jquery.mask.js';
	drupal_add_js($base_url."/".path_to_theme().'/js/jquery.npContextMenu.js');
	drupal_add_js($mask);
	drupal_add_js($base_url."/".path_to_theme().'/js/utils.js');
	// die($lib."/core/bootstrap.min.css");
	
?>


<script type="text/javascript">
var j = jQuery.noConflict();
j(document).keyup(function(e) {
	if (e.keyCode == 27) { 
		if(j("#overlay-close").length > 0){
			j("#overlay-close").click();
		}
	 }
});	

j(document).ready(function (){
	j('#sidebarControl img').click(function(){
		j("#sidebar-left").animate({width:'toggle'},350);
		if(j('#sidebarControl').css('left') == '0px'){
			j('#sidebarControl').animate({left:'242px'},350);
			j('#sidebarControl img').attr('src','<?=$base_url?>/sites/all/themes/adminimal_theme/images/setaMenuLateralRecolher.png');
		} else {
			j('#sidebarControl').animate({left:'0px'},350);
			j('#sidebarControl img').attr('src','<?=$base_url?>/sites/all/themes/adminimal_theme/images/setaMenuLateralExtender.png');
		}
	});

	
});
/*
var form = new FormData();
form.append("type", "agendamento");
form.append("title", "Agendamento srv2");
form.append("field_data[und][0][value][date]", "08/04/2020 - 19:00");
form.append("field_data[und][0][value2][date]", "08/04/2020 - 19:50");
form.append("field_profissional[und]", "95");
form.append("field_status[und]", "0");
form.append("body[und][0][value]", "OBS");
form.append("field_tipo_de_agendamento[und][0]", "0");
form.append("language", "pt-br");
form.append("field_paciente[und]", "82");
form.append("fgm_node_agendamento_form_group_procedimentos[fields][items][0][field_procedimentos][und][target_id]", "Teleconsulta\t(428)");
form.append("fgm_node_agendamento_form_group_procedimentos[fields][items][0][field_quantidade_do_procedimento][und][value]", "1");
form.append("fgm_node_agendamento_form_group_procedimentos[fields][items][0][field_valor][und][value]", "50,00");
form.append("fgm_node_agendamento_form_group_pagamentos[fields][items][0][field_forma][und]", "1");
form.append("fgm_node_agendamento_form_group_pagamentos[fields][items][0][field_valor_pago][und][value]", "50,00");
form.append("fgm_node_agendamento_form_group_pagamentos[fields][items][0][field_parcelas][und][value]", "1");
form.append("fgm_node_agendamento_form_group_pagamentos[fields][items][0][field_data_do_pagamento][und][value][date]", "04/27/2020");
form.append("form_build_id", "form-FTN9fpx3Aie_omT987_lCKxUYwqJYp4226E18N3hJ4g");
form.append("form_token", "_HAYgKQi3Sz29_uc7vd8OlJUjSVDpWy3znfmovmnlik");
form.append("form_id", "agendamento_node_form");
form.append("changed", "1586352568");

var settings = {
  "async": true,
  "crossDomain": true,
  "url": "https://cliente.samo.nesaude.com/srv/node/434",
  "method": "PUT",
  "headers": {
    "api-key": "Q_1RFryRrTTj_aZ69x75HA",
    "cache-control": "no-cache",
    "Access-Control-Allow-Origin:": "*",
    "Access-Control-Allow-Headers": "Content-Type",
    "postman-token": "f8765395-9379-6f33-7b7c-4d62a4cc7976"
  },
  "processData": false,
  "contentType": false,
  "mimeType": "multipart/form-data",
  "data": form
}

j.ajax(settings).done(function (response) {
  console.log(response);
});
*/
</script>
<?php 
<div id="loading" style="display:none"></div>
<div id="branding" class="clearfix">

	<?php //print $breadcrumb; ?>

	<?php print render($title_prefix); ?>

	<?php if ($title): ?>
		<h1 class="page-title"><?php print $title; ?></h1>
	<?php endif; ?>

	<?php print render($title_suffix); ?>

</div>

<div id="navigation">

  <?php if ($primary_local_tasks): ?>
    <?php print render($primary_local_tasks); ?>
  <?php endif; ?>

  <?php if ($secondary_local_tasks): ?>
    <div class="tabs-secondary clearfix"><ul class="tabs secondary"><?php print render($secondary_local_tasks); ?></ul></div>
  <?php endif; ?>

</div>

<div id="page">

	<div id="content" class="clearfix">
		<div class="element-invisible"><a id="main-content"></a></div>

	<?php if ($messages): ?>
		<div id="console" class="clearfix"><?php print $messages; ?></div>
	<?php endif; ?>

	<?php if ($page['help']): ?>
		<div id="help">
			<?php print render($page['help']); ?>
		</div>
	<?php endif; ?>

	<?php if (isset($page['content_before'])): ?>
		<div id="content-before">
			<?php print render($page['content_before']); ?>
		</div>
	<?php endif; ?>

	<?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

  <div id="content-wrapper">

	<?php if (isset($page['sidebar_left'])): ?>
		<?php 
			$q = explode('/',$_GET['q']);
			if($q[0] != 'admin' && $q[0] != 'prontuario'){
		?>	
		<link href='<?=$lib?>/core/bootstrap.min.css' rel='stylesheet' />
		<link href='<?=$lib?>/bootstrap/main.css' rel='stylesheet' />
		<div id="sidebar-left">
			<?php print render($page['sidebar_left']); ?>
		</div>
		<?php  } ?>
    <?php endif; ?>

    <div id="main-content">
	    <?php print render($page['content']); ?>
	  </div>

    <?php if (isset($page['sidebar_right'])): ?>
      <div id="sidebar-right">
        <?php print render($page['sidebar_right']); ?>
      </div>
    <?php endif; ?>
	
	</div>

	<?php if (isset($page['content_after'])): ?>
		<div id="content-after">
			<?php print render($page['content_after']); ?>
		</div>
	<?php endif; ?>

	</div>

</div>
<div id="footer">
	<?php print $feed_icons; ?>
	<b>SAMO</b> - <b>S</b>istema de <b>A</b>tendimento <b>M</b>édico e <b>O</b>dontológico
</div>
<ul id="superMenu" class="dropdown-menu" role="menu" style="display:none" >
	<li id="tituloMenu"><h7>Paciente</h7></li>
    <li><img src="<?=$base_url."/".path_to_theme()?>/icons/edita.png" /><a id="edita" tabindex="-1" href="#">Editar dados </a></li>
    <li><img src="<?=$base_url."/".path_to_theme()?>/icons/pront.png" /><a id="pront" tabindex="-1" href="#">Prontuário </a></li>
	<li><img src="<?=$base_url."/".path_to_theme()?>/icons/resum.png" /><a id="resum" tabindex="-1" href="#">Resumo financeiro </a></li>
	<li><img src="<?=$base_url."/".path_to_theme()?>/icons/orcam.png" /><a id="orcam" tabindex="-1" href="#">Orçamentos </a></li>
	<li><img src="<?=$base_url."/".path_to_theme()?>/icons/whats.png" /><a id="whats" tabindex="-1" href="#">WhatsApp </a></li>
</ul>
*/