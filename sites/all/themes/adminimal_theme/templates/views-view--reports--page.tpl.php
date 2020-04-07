<?php
global $base_url;
global $user;
$lib = $base_url."/".libraries_get_path('jsgrid');

?>
<link type="text/css" rel="stylesheet" href="<?=$lib?>/jsgrid.min.css" />
<link type="text/css" rel="stylesheet" href="<?=$lib?>/jsgrid-theme.min.css" />
    
<script type="text/javascript" src="<?=$lib?>/jsgrid.min.js"></script>
<script type="text/javascript">
var j = jQuery.noConflict();
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
            url: "<?=$base_url?>/admin/getProcedimentos?nid=<?=$agendamentoId?>",
            dataType: "json"
          });
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
</script>

