<?php
  global $base_url;
  $q = 0;
  $agendamento = 0;
  $paciente = 0;
  $agendamentoNid = 0;

  if(isset($_GET['q'])){

    $q = explode("/",$_GET['q']);
    $paciente = user_load($q[2]);
    $agendamento = node_load($q[3]);
    
  }

  if($agendamento){
    
    $agendamentoNid = $agendamento->nid;
    $dadosTitle = ' do Agendamento';
    $dataAgendamento = dataFormat($agendamento->field_data['und'][0]['value']);
    $hoje            = date("d/m/Y", time());

    // para os casos de parâmetros inválidos
    if($agendamento->field_paciente['und'][0]['target_id'] != $paciente->uid || !$paciente){
      header('Location: '.$base_url);
    }

  } else {
    $dadosTitle = ' do Paciente';
  }

?>
<script type="text/javascript">
var j = jQuery.noConflict();

function show(alvo){
  j("."+alvo).toggle();
}



</script>
  <fieldset class="dados">
    <legend>Dados<?=$dadosTitle?></legend>
    <sup><b>Paciente:</b></sup><h1><?=$paciente->field_nome_completo['und'][0]['value']?></h1>
<?php if(isset($agendamento->field_procedimentos['und'])){ ?>
    <?php foreach($agendamento->field_procedimentos['und'] as $k=>$v){ ?>
        <?php $procedimento = node_load($v['target_id']); ?>
        <?php $classExecutou = ($agendamento->field_executou['und'][$k]['value'])? 'EXEC':'PEND' ?>
        <li class="<?=$classExecutou?>">
            <?=$agendamento->field_quantidade_do_procedimento['und'][$k]['value']?>x 
            <?=$procedimento->title?> - 
            <?= $agendamento->field_obs_do_procedimento['und'][$k]['value'] ?>
        </li>
    <?php  }  ?>
  <?php  }  ?>
  </fieldset>
<?php if($agendamentoNid && $dataAgendamento == $hoje){ ?>
  <form action="<?=$base_url?>/salvarProntuario" method="POST" class="node-form">
    <div class="form-item form-type-textarea body">
      <label for="body">Ficha clínica </label>
    <div class="form-textarea-wrapper resizable textarea-processed resizable-textarea">
      <textarea class="text-full form-textarea" name="body" cols="60" rows="20"></textarea>
    </div>
    <input type="hidden" value="<?=$agendamentoNid?>" name="agendamento" />
    <input type="hidden" value="<?=$paciente->uid?>" name="paciente" />
    <div class="form-actions form-wrapper" id="edit-actions">
      <input type="submit" id="edit-submit" name="op" value="Salvar e finalizar" class="form-submit">
      <a href='<?=$base_url?>/node/add/orcamento' target="_blank" class="button overlay-exclude">Fazer orçamento</a>
     
    </div>
  </form>
<?php } ?>
<?php if (!$rows): ?>
  <fieldset>
  <legend>Histórico</legend>
    - Paciente sem histórico de atendimento -
</fieldset>
  <?php endif; ?>

<div class="<?php print $classes; ?>">
  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
    <?php print $title; ?>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
  <?php if ($header): ?>
    <div class="view-header">
      <?php print $header; ?>
    </div>
  <?php endif; ?>

  <?php if ($exposed): ?>
    <div class="view-filters">
      <?php print $exposed; ?>
    </div>
  <?php endif; ?>

  <?php if ($attachment_before): ?>
    <div class="attachment attachment-before">
      <?php print $attachment_before; ?>
    </div>
  <?php endif; ?>

  <?php if ($rows): ?>
    <div class="view-content">
      <?php print $rows; ?>
    </div>
  <?php elseif ($empty): ?>
    <div class="view-empty">
      <?php print $empty; ?>
    </div>
  <?php endif; ?>

  <?php if ($pager): ?>
    <?php print $pager; ?>
  <?php endif; ?>

  <?php if ($attachment_after): ?>
    <div class="attachment attachment-after">
      <?php print $attachment_after; ?>
    </div>
  <?php endif; ?>

  <?php if ($more): ?>
    <?php print $more; ?>
  <?php endif; ?>

  <?php if ($footer): ?>
    <div class="view-footer">
      <?php print $footer; ?>
    </div>
  <?php endif; ?>

  <?php if ($feed_icon): ?>
    <div class="feed-icon">
      <?php print $feed_icon; ?>
    </div>
  <?php endif; ?>

</div><?php /* class view */ ?>
