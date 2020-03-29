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
$uid = explode('/',$_GET['q']);
if(!isset($uid[1])){
  $uid[1] = "ALL";
}
//echo "<pre>";
//print_r($view->exposed_data); die();
$profissional = user_load($uid[1]);

$view->exposed_data['field_data_value']['min'] = ($view->exposed_data['field_data_value']['min'] == '')? date('Y-m-d') : $view->exposed_data['field_data_value']['min'];
$view->exposed_data['field_data_value']['max'] = ($view->exposed_data['field_data_value']['max'] == '')? date('Y-m-d') : $view->exposed_data['field_data_value']['max'];

  $start = $view->exposed_data['field_data_value']['min']." 00:00:00";
  $end = $view->exposed_data['field_data_value']['max']." 23:59:59";

  /*Link para impressão*/
  $s = explode('-',$view->exposed_data['field_data_value']['min']);
  $startURL = rawurlencode($s[2].'/'.$s[1].'/'.$s[0]);
  $e = explode('-',$view->exposed_data['field_data_value']['max']);
  $endURL = rawurlencode($e[2].'/'.$e[1].'/'.$e[0]);
 
  $produtividade = "?p=true&".rawurlencode("field_data_value[min][date]")."=$startURL &".rawurlencode("field_data_value[max][date]")."=$endURL";
  $link = $_GET['q'].$produtividade;

	if(!isset($_GET['field_data_value'])){
		header("Location: $produtividade");
	}
?>
<script>
  var j = jQuery.noConflict();

    j(document).ready(function(){

      //Pegar os valores do saldo TOTAL
      j.get("<?=$base_url?>/getSaldo?uid=<?=$uid[1]?>&start=<?=$start?>&end=<?=$end?>", function(ret){

        var saldo = JSON.parse(ret);

        let number = saldo[0].totalPago - saldo[0].totalExecutado;
        let reais = new Intl.NumberFormat('pt-BR', {
            style: 'decimal',
            currency: 'GBP',
            minimumFractionDigits: 2,
        }).format(saldo[0].totalExecutado);
        j('#executado .valor').html("R$ " + reais);

      });
    });
</script>
<fieldset class="dados view-display-id-block_pacientes">
  <legend><?=$profissional->field_nome_completo['und'][0]['value']?> (<?=$profissional->uid?>)</legend>
  <?php if(!isset($_GET['p'])){ ?>
    <div id="print">
      <a href="<?=$base_url?>/print/<?=$link?>" target="_blank"><img src="<?=$base_url?>/sites/all/themes/adminimal_theme/icons/print.png" /></a>
    </div>
  <?php } ?>
  <div class="card" id="periodo">
    <span class="cardTitle">PERÍODO</span>
    <sup class="cardSubTitle">Período considerado para o relatório</sup>
    <div class="periodo valor">
      <?=rawurldecode($startURL)?> a <?=rawurldecode($endURL)?>
    </div>
  </div>
  <div class="card" id="executado">
    <span class="cardTitle">EXECUTADO</span>
    <sup class="cardSubTitle">Consolidado de valores dos procedimentos EXECUTADOS pelo paciente</sup>
    <div class="valor debito"></div>
  </div>
  <div class="card" id="saldo">
    <span class="cardTitle">REPASSE</span>
    <sup class="cardSubTitle">Valor do repasse a ser realizado para o profissional</sup>
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
