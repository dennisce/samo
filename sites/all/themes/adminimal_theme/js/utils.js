
  var j = jQuery.noConflict();
  
j(document).ready(function(){
  var SPMaskBehavior = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
  },
  spOptions = {
    onKeyPress: function(val, e, field, options) {
        field.mask(SPMaskBehavior.apply({}, arguments), options);
      }
  };
  j("#edit-telefone").mask(SPMaskBehavior,spOptions);
  j("#edit-nascimento").mask("99/99/9999");

  addContextMenu();
  Drupal.behaviors.contextMenu = {
    attach: function(context, settings){
      addContextMenu();
    }
  };

  //Context
  j("#superMenu").superMenu({
    onMenuOptionSelected: function (invokedOn, selectedMenu) {
    
      let uid = invokedOn.className.split('uid-');
      let action = selectedMenu.id;
      switch (action) {
        case 'edita':
          selectedMenu.href = Drupal.settings.basePath+"user/"+uid[1]+"/edit";
          break;
        case 'pront':
          selectedMenu.href = Drupal.settings.basePath+"admin/prontuario/"+uid[1]+"/ALL";
          break;
        case 'laudo':
          selectedMenu.href = Drupal.settings.basePath+"admin/laudos/"+uid[1];
          break;
        case 'resum':
          selectedMenu.href = Drupal.settings.basePath+"resumo-financeiro/"+uid[1];
          break;
        case 'orcam':
          selectedMenu.href = Drupal.settings.basePath+"orcamentos/"+uid[1];
          break;
        case 'whats':
          enviaWpp(uid[1]);
          break;
        default:
          break;
      }
    }
  });
});


function autoCompleteJS(base_url,autoComplete){

    autoComplete = new jsGrid.Field({
        sorter: function(tag1, tag2) {
          return tag1.localeCompare(tag2);
        },
        itemTemplate: function(value) {
          return value;
        },
        insertTemplate: function(value) {
          return this._insertAuto = j("<input>").autocomplete({
            source : function(req,res){ buscaProcedimentos(base_url,res,req) },
            select : function(event, ui){ addComportamento(base_url,ui,'header') }
          }) 
        },
        editTemplate: function(value) {
          return this._editAuto = j("<input>").autocomplete({
            source : function(req,res){ buscaProcedimentos(base_url,res,req) },
            select : function(event, ui){ addComportamento(base_url,ui,'body') }
          }).val(value);
        },
        insertValue: function() {
          return this._insertAuto.val();
        },
        editValue: function() {
          return this._editAuto.val();
        }
      });

      return autoComplete;
}

function addComportamento(base_url, ui, local){
    let nid = ui.item.value.substring(
        ui.item.value.lastIndexOf("(") + 1, 
        ui.item.value.lastIndexOf(")")
      );
    j.get(base_url+'/admin/getDadosProcedimentos?nid='+nid, function(data){
      let d = JSON.parse(data);
      j('#jsGridProcedimentos .jsgrid-grid-'+local+' td.valor input').val(d.field_valor.und[0].value);
      j('#jsGridProcedimentos .jsgrid-grid-'+local+' td.idGrid input').val(d.nid);
      j('#jsGridProcedimentos .jsgrid-grid-'+local+' td.qtdGrid input').val(1);
    });
}

function moneyFieldJS(MoneyField){
  MoneyField = new jsGrid.NumberField({

    itemTemplate: function(value) {
        return "R$" + value.toFixed(2);
    },

    filterValue: function() {
        return parseFloat(this.filterControl.val() || 0);
    },

    insertValue: function() {
        return parseFloat(this.insertControl.val() || 0);
    },

    editValue: function() {
        return parseFloat(this.editControl.val() || 0);
    }

  });
  return MoneyField;
}

function dateTimeFieldJS(DateTimeField){
  var DateTimeField = new jsGrid.Field({
 
    css: "date-field",
    align: "center",              
 
    sorter: function(date1, date2) {
        return new Date(date1) - new Date(date2);
    },
 
    itemTemplate: function(value) {
      let dt = value.split('T');
      if(dt.length == 2){
        let dt = formatDate(value);
        return dt;
      } else {
        return value;
      }
        
    },
 
    insertTemplate: function(value) {
        this._insertPicker = j("<input>").datepicker({ defaultDate: new Date(), dateFormat: "dd/mm/yy" });
        return this._insertPicker.val(value);
    },
 
    editTemplate: function(value) {
        this._editPicker = j("<input>").datepicker({ defaultDate: new Date(), dateFormat: "dd/mm/yy" });
        let dt = value.split('T');
        if(dt.length == 2){
          let dt = formatDate(value);
          return this._editPicker.val(dt);
        } else {
          return this._editPicker.val(value);
        }
    },
 
    insertValue: function() {
        return this._insertPicker.datepicker("getDate").toISOString();
    },
 
    editValue: function() {
      return this._editPicker.datepicker("getDate").toISOString();
    }
});

  return DateTimeField;

}

function buscaProcedimentos(base_url, res,req){

  return j.get(base_url+'/entityreference/autocomplete/single/field_procedimentos/node/agendamento/NULL/'+req.term, function(data){
    if(data.length === 0){
      res(null);
    } else {
      let array = "[";
      for(var nome in data){
        array += '"'+nome+'",';
      }
      array = array.substring(0,(array.length - 1));
      array += "]";
      res(JSON.parse(array));
    }
  })
          
}

function setCookie(name,exdays,value){
  var expires;

  var date;
  var value;
  date = new Date(); 
  date.setTime(date.getTime()+(exdays*24*60*60*1000));
  expires = date.toUTCString();
  document.cookie = name+"="+value+"; expires="+expires+"; path=/";

}

function valor_cookie(nome_cookie) {
    var cname = ' ' + nome_cookie + '=';
    var cookies = document.cookie;
    if (cookies.indexOf(cname) == -1) {
        return false;
    }
    cookies = cookies.substr(cookies.indexOf(cname), cookies.length);
    if (cookies.indexOf(';') != -1) {
        cookies = cookies.substr(0, cookies.indexOf(';'));
    }
    cookies = cookies.split('=')[1];
    return decodeURI(cookies);
}

function formatDate(date) {
    var dt = date.split('T');
    if(dt.length == 2){
      let dt2 = dt[0].split('-');
      return dt2[2]+"/"+dt2[1]+"/"+dt2[0];
    } else {
      dt = date.split('-');
      return dt[2]+"/"+dt[1]+"/"+dt[0];
    }
}

function calculaSaldoTotal(base_url,pid){
  //Pegar os valores do saldo TOTAL
  j.get(base_url+"/admin/getSaldo?pid="+pid, function(ret){

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
}

function addContextMenu(){
	j(".view-display-id-block_pacientes .views-row").on("click contextmenu", function(e){
		j("#superMenu").trigger("npmenu:show",e);

		let uid = e.currentTarget.className.split('uid-');
		let nomePaciente = j(".uid-"+uid[1]+" .views-field-field-nome-completo .field-content").html();
				
		let bodyMargin = parseInt(j('body').css('margin-top').replace("px",''));
		let overlay = "0px";
    if(j('#overlay').css('padding-top')){
      overlay = j('#overlay').css('padding-top');
      nomePaciente = j(".uid-"+uid[1]).html();
    }
		let overlayPadding = parseInt(overlay.replace("px",''));
		let top = (bodyMargin+overlayPadding);
		j("#superMenu").css("top",(e.pageY-top*2)+"px");
    j("#superMenu").css("left",(e.pageX-top)+"px");
    j('#superMenu li h7').html(nomePaciente);
    		
	});
}

function enviaWpp(uid){

  var telefone = j(".telefone-"+uid).html();
  
  telefone = telefone.replace(/[^0-9\s]/gi, '').replace(/[_\s]/g, '');

  window.open("https://web.whatsapp.com/send?phone=+55"+telefone);

}

