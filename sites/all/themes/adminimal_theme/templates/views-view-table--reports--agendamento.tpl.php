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
$totalPeriodo = 0;
// print_r($statusValues);
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
            TOTAL
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
  <?php endif; ?>
  <tbody>
    <?php foreach ($rows as $row_count => $row): ?>
      <tr <?php if ($row_classes[$row_count]): ?> class="<?php print implode(' ', $row_classes[$row_count]); ?>  "<?php endif; ?>>
        <?php foreach ($row as $field => $content): ?>
          <?php
            $agendamento = node_load($content);
            $totalPago = 0;
            $class = str_replace(" ","",$statusValues[$agendamento->field_status['und'][0]['value']]);
          ?>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count];?> <?=$class?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <?php
              $dataHoraAgendamento = explode(" ",$agendamento->field_data['und'][0]['value']);
              $dataAgendamento = explode("-",$dataHoraAgendamento[0]);
              $dataAgendamento = $dataAgendamento[2]."/".$dataAgendamento[1]."/".$dataAgendamento[0];
            ?>
            <?=$dataAgendamento." às ".substr($dataHoraAgendamento[1],0,-3)?>h
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <?php $paciente = user_load($agendamento->field_paciente['und'][0]['target_id']) ?>
            <?= $paciente->field_nome_completo['und'][0]['value'] ?>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <?php $profissional = user_load($agendamento->field_profissional['und'][0]['target_id']) ?>
            <?= $profissional->field_nome_completo['und'][0]['value'] ?>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <ul>
            <?php
              if(isset($agendamento->field_procedimentos['und'])){ 
                foreach($agendamento->field_procedimentos['und'] as $k=>$v){
                  $procedimento = node_load($v['target_id']);
                  if($agendamento->field_executou['und'][$k]['value']){
                    echo "<li>$procedimento->title</li>";
                  }
                }  
              }
            ?>
            </ul>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <ul>
            <?php 
              if(isset($agendamento->field_forma['und'])){ 
                foreach($agendamento->field_forma['und'] as $k=>$v){
                  echo "<li>".$formaValues[$v['value']]."</li>";
                }  
              }
            ?>
            </ul>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <ul>
            <?php 
              if(isset($agendamento->field_valor_pago['und'])){ 
                  foreach($agendamento->field_valor_pago['und'] as $k=>$v){
                  echo "<li>R$ ".$v['value']."</li>";
                  $totalPago += floatval($v['value']);
                }  
              }
            ?>
            </ul>
          </td>
          <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
            R$ <?=$totalPago?>
            <?php $totalPeriodo += floatval($totalPago) ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    <tr class="footer">
            <td colspan="6">TOTAL</td>
            <td><?=$totalPeriodo?></td>
    </tr>
  </tbody>
</table>
