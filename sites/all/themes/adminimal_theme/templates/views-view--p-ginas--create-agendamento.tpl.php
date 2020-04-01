<div style="display:none" class="messages error"></div>
<?php
ini_set('max_execution_time', '3000');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

if(isset($_GET['dev'])){

  $n = node_load($_GET['dev']);
  echo "<pre>";
  print_r($n);

}

global $base_url;
global $user;
$lib = $base_url."/".libraries_get_path('jsgrid');


// Para a listagem de profissionais do input
function users_by_role($role_name) {
  $role = user_role_load_by_name($role_name);
  $query = 'SELECT ur.uid
    FROM {users_roles} AS ur
    WHERE ur.rid = :rid';
  $result = db_query($query, array(':rid' => $role->rid));
  $uids = $result->fetchCol();
  return user_load_multiple($uids);
}
drupal_add_library('system', 'ui.autocomplete');
$users = users_by_role('Profissional da Saúde');

// Recupera o paciente passado como parâmetro
// $q[count($q)-3] = agendamento
// $q[count($q)-2] = paciente
// $q[count($q)-1] = profissional

$q = explode('/',$_GET['q']);
$paciente = user_load($q[count($q)-2]);

// Recupera o agendamento passado como parâmetro
$q = explode('/',$_GET['q']);
$agendamentoId                    = '';
$agendamento                      = '';
$agendamentoStatusValue           = '';
$agendamentotipoAgendamentoValues = [0];
$agendamentoData                  = '';
$agendamentoHora                  = '';
if(isset($user->roles[4]) && !isset($user->roles[3])){

  $profissionalId                 = $user->uid;

} else {
  $profissionalId                 = $q[count($q)-1];
}

if($q[count($q)-3] != 'agendamento'){
  $agendamentoId                    = $q[count($q)-3];
  $agendamento                      = node_load($agendamentoId);
  $agendamentoStatusValue           = $agendamento->field_status['und'][0]['value'];
  $agendamentotipoAgendamentoValues = [];
foreach($agendamento->field_tipo_de_agendamento['und'] as $k=>$v){
    $agendamentotipoAgendamentoValues[] = $v['value']; 
  }
  $dataHora = explode(' ',$agendamento->field_data['und'][0]['value']);
  $dataHora[0] = explode('-',$dataHora[0]);

  $agendamentoData                  = $dataHora[0][2]."/".$dataHora[0][1]."/".$dataHora[0][0];
  $agendamentoHora                  = substr($dataHora[1],0,-3);
}

// Libs de javascript necessários para essa tela
drupal_add_js(path_to_theme().'/js/manipulaAgendamento.js');
drupal_add_js(path_to_theme().'/js/moment.min.js');

// Recupera os valores dos fields para os inputs
$all_fields_on_my_website = field_info_fields();
$tipoAgendamentoValues = list_allowed_values($all_fields_on_my_website["field_tipo_de_agendamento"]);
$statusValues = list_allowed_values($all_fields_on_my_website["field_status"]);


?>

<link type="text/css" rel="stylesheet" href="<?=$lib?>/jsgrid.min.css" />
<link type="text/css" rel="stylesheet" href="<?=$lib?>/jsgrid-theme.min.css" />
    
<script type="text/javascript" src="<?=$lib?>/jsgrid.min.js"></script>
<script type="text/javascript">
var j = jQuery.noConflict();

j(document).ready(function(){

  j('input#data').datepicker({ dateFormat: 'dd/mm/yy' });
  j('input#data').mask('99/99/9999');
  j('input#hora').mask('99:99');

  // Monta o AutoComplete
  var autoComplete = function(config) {
    jsGrid.Field.call(this, config);
  };
  autoComplete.prototype = autoCompleteJS('<?=$base_url?>',autoComplete.prototype);
  jsGrid.fields.autoComplete = autoComplete;
  
  // Monta o R$
  var MoneyField = function (config) {
    jsGrid.NumberField.call(this, config);
  }
  MoneyField.prototype = moneyFieldJS(MoneyField.prototype);
  jsGrid.fields.money = MoneyField;

  // Grid de procedimentos
  j("#jsGridProcedimentos").jsGrid({
      width: "100%",
      inserting: true,
      editing: true,
      sorting: true,
      paging: true,
      controller: {
        loadData: function(filter) {
          return j.ajax({
            url: "<?=$base_url?>/getProcedimentos?nid=<?=$agendamentoId?>",
            dataType: "json"
          });
        }
      },
      onItemInserting: function(row) {
        row.cancel = !manipulaAgendamento('<?=$base_url?>',row,'addPro');
        if(row.cancel){
          j("#jsGridProcedimentos").jsGrid("editItem", row.item);
        }
      },
      onItemUpdated: function(row){
        row.cancel = !manipulaAgendamento('<?=$base_url?>',row,'updPro');
        if(row.cancel){
          j("#jsGridProcedimentos").jsGrid("editItem", row.item);
        }
      },
      onItemDeleting: function(row){
        row.cancel = !manipulaAgendamento('<?=$base_url?>',row,'delPro');
        if(row.cancel){
          j("#jsGridProcedimentos").jsGrid("editItem", row.item);
        }
      },
      onRefreshed: function(grid){
        calculaTotais();
      },
      invalidNotify: function(item) {return false},
      fields: [
          { name: "id", sorting:false, css:"idGrid", type: "number", width: 10, validate: "required"},
          { name: "Procedimento", sorting:false, css:"procedimentoGrid", type: 'autoComplete', width: 150, validate: "required" },
          { name: "QTD", sorting:false, css:'qtdGrid', type: "number", width: 40, validate:function(value,item){
            return value > 0;
          }  },
          { name: "Valor", sorting:false, css:'valor', type: "money", width: 50,
            validate:function(value,item){
              return value >= 0;
            }
        },
          { name: "Detalhes", sorting:false, type: "text", width: 200 },
          { name: "Executou", sorting:false, type: "checkbox", title: "Executou?", sorting: false },
          { type: "control" }
      ],
      
  });

  var formaPgto = [
      {name: 'Dinheiro', id:0},
      {name: 'Cartão de Crédito', id:1},
      {name: 'Cartão de Débito', id:2},
      {name: 'Boleto', id:3},
      {name: 'Cheque', id:4},
      {name: 'Convênio', id:5},
    ];

  // Monta o DateTime
  var DateTimeField = function (config) {
      jsGrid.Field.call(this, config);
  };
  DateTimeField.prototype = dateTimeFieldJS(DateTimeField.prototype);
  jsGrid.fields.dateTime = DateTimeField;

  // Grid de pagamentos
  j("#jsGridPagamentos").jsGrid({
      width: "100%",
      inserting: true,
      editing: true,
      sorting: true,
      paging: true,
      controller: {
        loadData: function(row) {
          return j.ajax({
            url: "<?=$base_url?>/getPagamentos?nid=<?=$agendamentoId?>",
            dataType: "json"
          });
        }
      },
      invalidNotify: function(item) {return false;},
      onItemInserting: function(row) {
        row.cancel = !manipulaAgendamento('<?=$base_url?>',row,'addPag');
      },
      onItemUpdated: function(row){
        row.cancel = !manipulaAgendamento('<?=$base_url?>',row,'updPag');
      },
      onItemDeleting: function(row){
        row.cancel = !manipulaAgendamento('<?=$base_url?>',row,'delPag');
      },
      onRefreshed: function(grid){
        calculaTotais();
      },
      fields: [
          { name: "formaPgto", type: 'select', validate: "required", items: formaPgto, valueField: "id", textField: "name", headerTemplate:"Forma de pagamento"  },
          { name: "valorPgto",textField:"Valor", css:'valorPago', type: "money", width: 50, headerTemplate:"Valor pago", validate:function(value,item){return value > 0;}},
          { name: "parcelas", type: "number", validate: "required", width: 200, headerTemplate:"Parcelas", validate:function(value,item){return value > 0;}},
          { name: "dataPgto", type: "dateTime", validate: "required", headerTemplate:"Data do pagamento",validate:function(value,item){return value != '';}},
          { type: "control" }
      ],
      
  });

  calculaSaldoTotal("<?=$base_url?>",<?=$paciente->uid?>);

  <?php
    // Verifica se é necessário preencher campos com momento atual  
  ?>
  <?php if(!$agendamento){ ?>
  
  var dateClick = valor_cookie('dateClick');
  let inputData = formatDate(dateClick);
  let inputHora = dateClick.substr(11,5);
  j('input#data').val(inputData);

  if(inputHora){  
    j('input#hora').val(inputHora);
  } else {
    let inputHoraAgora = new Date();
    j('input#hora').val(inputHoraAgora.getHours()+":"+inputHoraAgora.getMinutes());
  }

<?php } else { ?>

  j("#jsGridProcedimentos").jsGrid("loadData");
  j("#jsGridPagamentos").jsGrid("loadData");

<?php } ?>

  });


</script>
<div id="voltar">
  <a onclick="window.history.back()" href="#">
    <img src="<?=$base_url."/".path_to_theme()?>/icons/back.png" width="15px" />
  </a>
</div>
  <input type="hidden" name="idAgendamento" value="<?=$agendamentoId?>" id="idAgendamento" />
  <select name="statusAgendamento" class="form-select statusAgendamento" id="status">
    <?php foreach($statusValues as $k=>$v){ ?>
    <?php $selected = ($k == $agendamentoStatusValue)? 'selected':''; ?>
    <option <?=$selected?> value="<?=$k?>"><?=$v?></option>
    <?php } ?>
  </select>
  <fieldset class="dados view-display-id-block_pacientes">
    <legend>Dados do Agendamento</legend>
    <div class="views-field-field-nome-completo">
      <sup><b>Paciente:</b></sup>
      <h1 class="views-row field-content uid-<?=$paciente->uid?>"><?=$paciente->field_nome_completo['und'][0]['value']?> (<?=$paciente->uid?>)</h1>
      <div class="views-field-field-telefone">
        <div class="field-content telefone-<?=$paciente->uid?>"><?=$paciente->field_telefone['und'][0]['value']?></div>
      </div>
    </div>
    <input type="hidden" name="idPaciente" id="idPaciente" value="<?=$paciente->uid?>" />
    <ul id="menuAgendamento">
      <li><sup><b>Dia:</b></sup></li>
      <li><input size="12" type="text" value="<?=$agendamentoData?>" name="dataAgendamento" id="data" class="date-clear form-text"/></li>
      <li><sup><b>às:</b></sup></li>
      <li><input size="5" type="text" value="<?=$agendamentoHora?>" name="horaAgendamento" id="hora" class="date-clear form-text"/></li>
      <li><sup><b>com:</b></sup></li>
      <li>
      <select name="profissional" class="form-select profissional" id="selectProifissional">
          <option value="nenhum">Selecione...</option>
          <?php foreach($users as $id=>$profissional){ ?>
          <?php $selected = ($id==$profissionalId)?'selected':'' ?>
          <option <?=$selected?> value="<?=$id?>"><?=$profissional->field_nome_completo['und'][0]['value']?></option>
          <?php } ?>
        </select>
      </li>
    </ul>
  </fieldset>
  <fieldset class="tipoAgendamento">
  <legend>Tipo do agendamento</legend>
    <?php foreach($tipoAgendamentoValues as $k=>$v){ ?>
    <?php $checked = (in_array($k,$agendamentotipoAgendamentoValues))?'checked':''; ?>
      <label><input value='<?=$k?>' type='checkbox' name='field_tipo_de_agendamento[]' <?=$checked?>><?=$v?></label>
    <?php } ?>
  </fieldset>

<?php

// Criação dos botões de ação
  $bto = array();
  $i = 0;
  $bto[$i]['class']   = 'button';
  $bto[$i]['attr']    = 'onClick="manipulaAgendamento(\''.$base_url.'\',\'\',\'bto\')"';
  $bto[$i]['value']   = 'SALVAR ALTERAÇÕES';
  
  if($agendamentoStatusValue != 4 && $agendamentoStatusValue != 6){

    if($agendamentoStatusValue != 1){
      // Agendamento não confirmado
      $i++;
      $bto[$i]['class']   = 'button-green';
      $bto[$i]['attr']    = 'onClick="alteraStatus(\''.$base_url.'\',\'1\')"';
      $bto[$i]['value']   = 'CONFIRMAR';
    }
    
    if($agendamentoStatusValue != 2){
      // Agendamento confirmado
      $i++;
      $bto[$i]['class']   = 'button-orange';
      $bto[$i]['attr']    = 'onClick="alteraStatus(\''.$base_url.'\',\'2\')"';
      $bto[$i]['value']   = 'EM ESPERA';
    }
    
    if(($user->uid == $profissionalId || isset($user->roles[3]) || isset($user->roles[4]))){
      $i++;
      $bto[$i]['class']   = 'button-purple';
      $bto[$i]['attr']    = 'href="'.$base_url.'/admin/prontuario/'.$paciente->uid.'/'.$agendamentoId.'" onClick="alteraStatus(\''.$base_url.'\',\'3\')"';

      if($agendamentoStatusValue == 3){
        $bto[$i]['value']   = 'ABRIR ATENDIMENTO';
        $i++;
        $bto[$i]['class']   = 'button-blue';
        $bto[$i]['attr']    = 'onClick="alteraStatus(\''.$base_url.'\',\'4\')"';
        $bto[$i]['value']   = 'FINALIZAR';

      } else {
        $bto[$i]['value']   = 'ATENDER';
      }
    }

    if($agendamento){
      $i++;
      $bto[$i]['class']   = 'button-red';
      $bto[$i]['attr']    = 'onClick="alteraStatus(\''.$base_url.'\',\'5\')"';
      $bto[$i]['value']   = 'EXCLUIR';
    }

  }

  $width = 100/($i+1);

?>
  <div id="alert-error-not-submit"></div>
  <ul id="menuAgendamento">
    <?php foreach($bto as $k=>$v){ ?>
      <li style="width:<?=$width?>%"><a class="<?=$v['class']?>" <?= $v['attr'] ?> ><?=$v['value']?></a></li>
    <?php } ?>
  </ul>
  <div id="jsGridProcedimentos"></div>
  <div id="totalProcedimentos">TOTAL A PAGAR: R$ <span></span></div>
  <div id="jsGridPagamentos"></div>
  <div id="totalPagamentos">TOTAL PAGO: R$ <span></span></div>
  <div id="pendente">FALTA PAGAR: R$ <span></span></div>
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
