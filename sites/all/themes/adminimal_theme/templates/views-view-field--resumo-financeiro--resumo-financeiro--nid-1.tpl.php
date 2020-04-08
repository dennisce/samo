<?php

/**
 * @file
 * This template is used to print a single field in a view.
 *
 * It is not actually used in default Views, as this is registered as a theme
 * function which has better performance. For single overrides, the template is
 * perfectly okay.
 *
 * Variables available:
 * - $view: The view object
 * - $field: The field handler object that can process the input
 * - $row: The raw SQL result that can be used
 * - $output: The processed output that will normally be used.
 *
 * When fetching output from the $row, this construct should be used:
 * $data = $row->{$field->field_alias}
 *
 * The above will guarantee that you'll always get the correct data,
 * regardless of any changes in the aliasing that might happen if
 * the view is modified.
 */
    
    global $base_url;
    $pid = explode('/',$_GET['q']);
    $opts = array(
    'http'=>array(
        'method'=>"GET",
        'header'=>"api-key:".API_KEY
    )
    );
    
    $context = stream_context_create($opts);
    $extrato = json_decode(file_get_contents($base_url."/admin/getSaldo?pid=".$pid[1]."&nid=".$output,false,$context));
    $saldo = floatval($extrato[0]->totalPago - $extrato[0]->totalExecutado);
    $linha = $row->_field_data['nid']['entity'];
?>
<div class="detalharAgendamento">
    <a href="<?=$base_url?>/admin/create/agendamento/<?=$row->nid?>/<?=$pid[1]?>/<?=$linha->field_profissional['und'][0]['target_id']?>">
        <img src="<?=$base_url?>/sites/all/themes/adminimal_theme/icons/info.png" />
    </a>
</div>
<ul class="resumo">
    <li class="credito"><b>PAGO: </b>R$ <?=number_format($extrato[0]->totalPago,'2',',','.')?></li>
    <li class="debito"><b>EXECUTADO: </b>R$ <?=number_format($extrato[0]->totalExecutado,'2',',','.')?></li>
    <li class="<?= (floatval($saldo) < 0)?"debito":"credito" ?>"><b>SALDO: </b>R$ <?=number_format($saldo,'2',',','.')?></li>
</ul>

<pre>
