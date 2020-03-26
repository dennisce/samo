function manipulaAgendamento(base_url,row,action){
    j("#loading").show();

    var agendamento = [];
    agendamento['PACIENTE']             = j('#idPaciente').val();
    agendamento['DATA']                 = j('#data').val();
    agendamento['HORA']                 = j('#hora').val();
    agendamento['PROFISSIONAL']         = j('#selectProifissional').val();
    agendamento['STATUS']               = j('#status').val();
    agendamento['ERRO']                 = false;
    agendamento['TIPO DE AGENDAMENTO']  = new Array();

    //Consolida os tipos de agendamento selecionados
    j.each(j("input[name='field_tipo_de_agendamento[]']:checked"), function(){
        agendamento['TIPO DE AGENDAMENTO'].push(j(this).val());
    });

    //Validação
    j(".error").html(''); 
    j(".error").hide();
    for(let i in agendamento){
        if((agendamento[i] == '' || !agendamento[i]) && i != 'ERRO'){
            j(".error").append('O campo <b>'+i+'</b> precisa ser preenchido corretamente <br />');
            agendamento['ERRO'] = true;
        }
    }

    if(agendamento['PROFISSIONAL'] == 'nenhum'){
        agendamento['ERRO'] = true;
        j(".error").append("Selecione o <b>PROFISSIONAL</b> <br />");
    }

    let h = agendamento['HORA'].split(':');
    if(h[0] > 23 || h[0] < 0 || h[1] > 59 || h[1] < 0){
        agendamento['ERRO'] = true;
        j(".error").append("O campo <b>HORA</b> precisa ser preenchido corretamente <br />");
    }

    if(agendamento['ERRO']){
        j(".error").show();
        j("#loading").hide();
        return false;
    }

    // Foram adicionados nesse momento para não participarem da validação
    agendamento['TIPO DE AGENDAMENTO']  = Object.assign({},agendamento['TIPO DE AGENDAMENTO']);
    agendamento['ID']                   = j('#idAgendamento').val();
    
    // Preenche OBJ de acordo com a ação
    if(row){
        agendamento = preencheAgendamento(row,agendamento,action);
    }

    //Envia para salvar o node AGENDAMENTO
    j.post(base_url+"/manipulaAgendamento", Object.assign({},agendamento), function(ret){

        j('#idAgendamento').val(ret);
        if(action == 'bto'){
            // location.href = base_url;
            j('#overlay-close').trigger('click');
        }

        j("#loading").hide();

    }, "json");

    
    calculaTotais();
    return true;

}

function alteraStatus(base_url,status){

    j("#status").val(status);
    if(status == 3){
        if(manipulaAgendamento(base_url,null,'atd')){
            let paciente = j('#idPaciente').val();
            let agendamento = j('#idAgendamento').val();
            location.href = "#admin/prontuario/"+paciente+"/"+agendamento;
        }
        
    } else {
        manipulaAgendamento(base_url,null,'bto');
    }

}

function preencheAgendamento(row,agendamento,action){
    // Define o delta do item a ser inserido
    let rowIndex;
    if(action.indexOf("add") != -1 ){
        rowIndex = (row.grid.data.length == 0)?0:row.grid.data.length +1;
    } else {
        rowIndex = row.itemIndex;
    }

    if(action.indexOf("Pro") != -1){
        // Grid de PROCDIMENTOS (já vem validada)
        agendamento['PROCEDIMENTO']         = {
            id          : row.item.id,
            Executou    : row.item.Executou,
            QTD         : row.item.QTD,
            Valor       : row.item.Valor,
            Detalhes    : row.item.Detalhes,
            index       : rowIndex,
            action      : action
        };
    } else if(action.indexOf("Pag") != -1){
        // Grid de PAGAMENTOS (já vem validada)
        agendamento['PAGAMENTO']         = {
            FormaPgto   : row.item.formaPgto,
            ValorPgto   : row.item.valorPgto,
            Parcelas    : row.item.parcelas,
            DataPgto    : row.item.dataPgto,
            index       : rowIndex,
            action      : action
        };
    }
    return agendamento;

}

function calculaTotais(){
    var totalProcedimentos = 0.00;
    j("#jsGridProcedimentos .jsgrid-grid-body .valor").each(function(){
        let valor = parseFloat(j(this).html().replace('R$',''));
        if(!isNaN(valor)){
            totalProcedimentos += valor;
        }
    });
    
    let reais = new Intl.NumberFormat('pt-BR', {
        style: 'decimal',
        currency: 'GBP',
        minimumFractionDigits: 2,
    }).format(totalProcedimentos);
    j("#totalProcedimentos span").html(reais);

    var totalPagamentos = 0;
    j("#jsGridPagamentos .jsgrid-grid-body .valorPago").each(function(){
        let valor = parseFloat(j(this).html().replace('R$',''));
        if(!isNaN(valor)){
            totalPagamentos += valor;
        }
    });
    reais = new Intl.NumberFormat('pt-BR', {
        style: 'decimal',
        currency: 'GBP',
        minimumFractionDigits: 2,
    }).format(totalPagamentos);
    j("#totalPagamentos span").html(reais);

    var pendente = totalProcedimentos - totalPagamentos;
    reais = new Intl.NumberFormat('pt-BR', {
        style: 'decimal',
        currency: 'GBP',
        minimumFractionDigits: 2,
    }).format(pendente);
    j("#pendente span").html(reais);

}