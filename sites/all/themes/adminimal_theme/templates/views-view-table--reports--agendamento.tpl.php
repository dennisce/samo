<?php

/**
 * @file
 * Template to display a view as a table.
 *
 * - $title : The title of this group of rows.  May be empty.
 * - $header: An array of header labels keyed by field id.
 * - $caption: The caption for this table. May be empty.
 * - $header_classes: An array of header classes keyed by field id.
 * - $fields: An array of CSS IDs to use for each field id.
 * - $classes: A class or classes to apply to the table, based on settings.
 * - $row_classes: An array of classes to apply to each row, indexed by row
 *   number. This matches the index in $rows.
 * - $rows: An array of row items. Each row is an array of content.
 *   $rows are keyed by row number, fields within rows are keyed by field ID.
 * - $field_classes: An array of classes to apply to each field, indexed by
 *   field id, then row number. This matches the index in $rows.
 *
 * @ingroup views_templates
 */
// Recupera os valores dos fields para os inputs
$all_fields_on_my_website = field_info_fields();
$tipoAgendamentoValues = list_allowed_values($all_fields_on_my_website["field_tipo_de_agendamento"]);
$statusValues = list_allowed_values($all_fields_on_my_website["field_status"]);
$formaValues = list_allowed_values($all_fields_on_my_website["field_forma"]);
$qtdAgendados = 0;
$qtdEmAtendimento = 0;
$qtdEmEspera = 0;
$qtdConfirmados = 0;
$qtdAgendamentos = 0;
$qtdAtendidos = 0;
$qtdCancelados = 0;
$qtdFaltas = 0;
$totalPeriodo = 0;
global $base_url;
?>
<table <?php if ($classes): ?> class="<?php print $classes; ?>"<?php endif ?><?php print $attributes; ?>>
   <?php if (!empty($title) || !empty($caption)): ?>
     <caption><?php print $caption . $title; ?></caption>
  <?php endif; ?>
  <?php if (!empty($header)) : ?>
    <thead>
      <tr>
        <?php foreach ($header as $field => $label): ?>
          <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
            Data do agendamento
          </th>
          <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
            Paciente
          </th>
          <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
            Profissional
          </th>
          <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
            Procedimentos realizados
          </th>
          <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
            Formas de pagamento
          </th>
          <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
            Pagametnos
          </th>
          <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
            SALDO
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
  <?php endif; ?>
  <tbody class="view-display-id-block_pacientes">
    <?php foreach ($rows as $row_count => $row): ?>
      <tr <?php if ($row_classes[$row_count]): ?> class="<?php print implode(' ', $row_classes[$row_count]); ?>  "<?php endif; ?>>
        <?php foreach ($row as $field => $content): ?>
          <?php

            $qtdAgendamentos++;
            $agendamento = node_load($content);
            $totalPago = 0;
            $totalExecutado = 0;
            $class = str_replace(" ","",$statusValues[$agendamento->field_status['und'][0]['value']]);
            $paciente = user_load($agendamento->field_paciente['und'][0]['target_id']);
            $profissional = user_load($agendamento->field_profissional['und'][0]['target_id']);

            switch ($agendamento->field_status['und'][0]['value']) {
              case '0':
                $qtdAgendados++;
                break;
              case '1':
                $qtdConfirmados++;
                break;
              case '2':
                $qtdEmEspera++;
                break;
              case '3':
                $qtdEmAtendimento++;
                break;
              case '4':
                $qtdAtendidos++;
                break;
              case '5':
                $qtdCancelados++;
                break;
              case '6':
                $qtdFaltas++;
                break;
              default:
                # code...
                break;
            }
          ?>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count];?> <?=$class?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <?php
              $dataHoraAgendamento = explode(" ",$agendamento->field_data['und'][0]['value']);
              $dataAgendamento = explode("-",$dataHoraAgendamento[0]);
              $dataAgendamento = $dataAgendamento[2]."/".$dataAgendamento[1]."/".$dataAgendamento[0];
            ?>
            <a href="<?=$base_url?>/admin/create/agendamento/<?=$agendamento->nid?>/<?=$paciente->uid?>/<?=$agendamento->field_profissional['und'][0]['target_id']?>">
              <?=$dataAgendamento." às ".substr($dataHoraAgendamento[1],0,-3)?>h
            </a>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="views-field-field-nome-completo <?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
              <span class="views-row field-content uid-<?=$paciente->uid?>"><?= $paciente->field_nome_completo['und'][0]['value'] ?></span><br />
              <div class="views-field-field-telefone">
                <div class="field-content telefone-<?=$paciente->uid?>"><?=$paciente->field_telefone['und'][0]['value']?></div>
              </div>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <?= $profissional->field_nome_completo['und'][0]['value'] ?>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <ol>
            <?php
              if(isset($agendamento->field_procedimentos['und'])){ 
                foreach($agendamento->field_procedimentos['und'] as $k=>$v){
                  $procedimento = node_load($v['target_id']);
                  if($agendamento->field_executou['und'][$k]['value']){
                    echo "<li>$procedimento->title</li>";
                    $totalExecutado += $agendamento->field_valor['und'][$k]['value'];
                  }
                }  
              }
            ?>
            </ol>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <ol>
            <?php 
              if(isset($agendamento->field_forma['und'])){ 
                foreach($agendamento->field_forma['und'] as $k=>$v){
                  echo "<li>".$formaValues[$v['value']]."</li>";
                }  
              }
            ?>
            </ol>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <ol>
            <?php 
              if(isset($agendamento->field_valor_pago['und'])){ 
                  foreach($agendamento->field_valor_pago['und'] as $k=>$v){
                  echo "<li>R$ ".number_format($v['value'], 2, ',', '.')."</li>";
                  $totalPago += floatval($v['value']);
                }  
              }
            ?>
            </ol>
          </td>
          
          <?php 
            $saldo = $totalPago - $totalExecutado;
            $totalPeriodo += $saldo; 
          ?>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?= ($saldo>=0)?"credito":"debito" ?> <?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            
              R$ <?=number_format(($saldo), 2, ',', '.')?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    <tr class="footer">
      <td colspan="6" style="text-align:center"><?=$qtdAgendamentos?> Agendamentos</td>
      <td class="<?= ($totalPeriodo>=0)?"credito":"debito" ?>">R$ <?=number_format($totalPeriodo, 2, ',', '.')?></td>
    </tr>
    <tr class="footer">
            <td class="Faltou"><span class="qtd"><?=$qtdFaltas?></span> Faltas</td>
            <td class="Agendado"><span class="qtd"><?=$qtdAgendados?></span> Agendados</td>
            <td class="Confirmado"><span class="qtd"><?=$qtdConfirmados?></span> Confirmados</td>
            <td class="Emespera"><span class="qtd"><?=$qtdEmEspera?></span> Em espera</td>
            <td colspan="2" class="Ematendimento"><span class="qtd"><?=$qtdEmAtendimento?></span> Em atendimento</td>
            <td class="Atendido"><span class="qtd"><?=$qtdAtendidos?></span> Atendidos</td>
    </tr>
  </tbody>
</table>
