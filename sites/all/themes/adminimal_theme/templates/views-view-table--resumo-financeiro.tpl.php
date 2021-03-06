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
global $base_url;
$totalPago = 0.00;
$pid = explode('/',$_GET['q']);
if(!isset($pid[1])){
  $pid[1] = "ALL";
}

$paciente = user_load($pid[1]);
?>
<script>
    j(document).ready(function(){

      //Pegar os valores do saldo TOTAL
      j.get("<?=$base_url?>/admin/getSaldo?pid=<?=$pid[1]?>", function(ret){

        var saldo = JSON.parse(ret);

        let number = saldo[0].totalPago - saldo[0].totalExecutado;
        let reais = new Intl.NumberFormat('pt-BR', {
            style: 'decimal',
            currency: 'GBP',
            minimumFractionDigits: 2,
        }).format(number);
        j('#saldo .valor').html("<b>R$ " + reais + "</b>");
        let situacao = (number < 0)?"debito":"credito";
        j('#saldo .valor').addClass(situacao);

        reais = new Intl.NumberFormat('pt-BR', {
            style: 'decimal',
            currency: 'GBP',
            minimumFractionDigits: 2,
        }).format(saldo[0].totalExecutado);
        j('#executado .valor').html("R$ " + reais);
        
        reais = new Intl.NumberFormat('pt-BR', {
            style: 'decimal',
            currency: 'GBP',
            minimumFractionDigits: 2,
        }).format(saldo[0].totalPago);
        j('#pago .valor').html("R$ " + reais);

      });
    });
</script>
<fieldset class="dados view-display-id-block_pacientes">
<legend>Dados do Paciente</legend>
  <div class="views-field-field-nome-completo">
    <sup><b>Paciente:</b></sup>
    <h1 class="views-row field-content uid-<?=$paciente->uid?>"><?=$paciente->field_nome_completo['und'][0]['value']?> (<?=$paciente->uid?>)</h1>
    <div class="views-field-field-telefone">
      <div class="field-content telefone-<?=$paciente->uid?>"><?=$paciente->field_telefone['und'][0]['value']?></div>
    </div>
  </div>

  <div class="card" id="pago">
    <span class="cardTitle">PAGO</span>
    <sup class="cardSubTitle">Consolidado de valores pagos pelo paciente</sup>
    <div class="valor credito"></div>
  </div>
  <div class="card" id="executado">
    <span class="cardTitle">EXECUTADO</span>
    <sup class="cardSubTitle">Consolidado de valores dos procedimentos EXECUTADOS pelo paciente</sup>
    <div class="valor debito"></div>
  </div>
  <div class="card" id="saldo">
    <span class="cardTitle">SALDO</span>
    <sup class="cardSubTitle">Com base no que já foi pago <b>MENOS</b> o que foi executado</sup>
    <div class="valor"></div>
  </div>
  <table <?php if ($classes): ?> class="<?php print $classes; ?>"<?php endif ?><?php print $attributes; ?>>
    <?php if (!empty($title) || !empty($caption)): ?>
      <caption><?php print $caption . $title; ?></caption>
    <?php endif; ?>
    <?php if (!empty($header)) : ?>
      <thead>
        <tr>
          <?php foreach ($header as $field => $label): ?>
            <th <?php if ($header_classes[$field]): ?> class="<?php print $header_classes[$field]; ?>"<?php endif; ?> scope="col">
              <?php print $label; ?>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
    <?php endif; ?>
    <tbody>
      <?php foreach ($rows as $row_count => $row): ?>
        <tr <?php if ($row_classes[$row_count]): ?> class="<?php print implode(' ', $row_classes[$row_count]); ?>"<?php endif; ?>>
          <?php foreach ($row as $field => $content): ?>
            <td <?php if ($field_classes[$field][$row_count]): ?> class="<?php print $field_classes[$field][$row_count]; ?>"<?php endif; ?><?php print drupal_attributes($field_attributes[$field][$row_count]); ?>>
              <?php print $content; ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</fieldset>
