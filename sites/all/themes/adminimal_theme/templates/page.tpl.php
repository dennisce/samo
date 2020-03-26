<?php
	global $base_url;
	$lib = $base_url."/".libraries_get_path('calendarUtil');
	$mask = $base_url."/".libraries_get_path('maskedinput') . '/src/jquery.mask.js';
	drupal_add_js($base_url."/".path_to_theme().'/js/jquery.npContextMenu.js');
	drupal_add_js($mask);

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

	//Context
	j("#superMenu").superMenu({
	    onMenuOptionSelected: function (invokedOn, selectedMenu) {
			let uid = invokedOn.className.split('uid-');
			let action = selectedMenu.id;
			switch (action) {
				case 'edita':
					location.href = "#overlay=user/"+uid[1]+"/edit";
					break;
				case 'pront':
					location.href = "#overlay=/admin/prontuario/"+uid[1]+"/ALL";
					break;
				case 'resum':
					location.href = "#overlay=/resumo-financeiro/"+uid[1];
					break;
				case 'orcam':
					location.href = "#overlay=orcamentos/"+uid[1];
					break;
				case 'whats':
					
					break;
				default:
					break;
			}
    	}
	});

	addContextMenu();
	Drupal.behaviors.contextMenu = {
		attach: function(context, settings){
			addContextMenu();
		}
	};

});

function addContextMenu(){
	j(".view-display-id-block_pacientes li").on("click contextmenu", function(e){
		let uid = e.currentTarget.className.split('uid-');
		let nomePaciente = j(".uid-"+uid[1]+" span.views-field-field-nome-completo span").html();
		j('#superMenu li h7').html(nomePaciente);
		j("#superMenu").trigger("npmenu:show",e);
	});
}

</script>

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
