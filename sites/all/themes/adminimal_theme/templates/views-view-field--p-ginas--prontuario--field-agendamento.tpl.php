<?php
    $output = explode("|",$output);
    $agendamento = node_load($output[0]);
    $prontuario = node_load($output[1]);
    $dataAgendamento = date("d/m/Y - G:i", $prontuario->created);
    $profissional = user_load($prontuario->uid);
?>
<h1>
    Atendido em: <b><?= $dataAgendamento ?>h</b> por: <b><?=$profissional->field_nome_completo['und'][0]['safe_value']?></b>
</h1>
<fieldset>
    <legend>Procedimentos</legend>
    <ul>
        <?php foreach($agendamento->field_procedimentos['und'] as $k=>$v){ ?>
            <?php $procedimento = node_load($v['target_id']); ?>
            <?php $classExecutou = ($agendamento->field_executou['und'][$k]['value'])? 'EXEC':'PEND' ?>
            <li class="<?=$classExecutou?>"> 
                <?=$agendamento->field_quantidade_do_procedimento['und'][$k]['value']?>x 
                <?=$procedimento->title?> - 
                <?= $agendamento->field_obs_do_procedimento['und'][$k]['value'] ?>
            </li>
        <?php  }  ?>
    </ul>
</fieldset>