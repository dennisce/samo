var j = jQuery.noConflict();
var nomeAnterior = '';
j(document).ready(function (){
    j('#autocompleteNomePaciente').on('focusout',function(data){
        if(j(this).val() != nomeAnterior){
            j('input[name ="uid"]').val(' ');
        }
    });
    j('#autocompleteNomePaciente').bind('autocompleteSelect', function(event, node) {
        let dadosPaciente = j('#autocompleteNomePaciente').val();
        nomeAnterior = dadosPaciente;
        var uid = dadosPaciente.substring(
            dadosPaciente.lastIndexOf("(") + 1, 
            dadosPaciente.lastIndexOf(")")
        );
        j('input[name ="uid"]').val(uid);

        var telefone = dadosPaciente.substring(
            dadosPaciente.lastIndexOf("<") + 1, 
            dadosPaciente.lastIndexOf(">")
        );
        j("#edit-telefone").val(telefone);

        var nascimento = dadosPaciente.substring(
            dadosPaciente.lastIndexOf("[") + 1, 
            dadosPaciente.lastIndexOf("]")
        );
        j("#edit-nascimento").val(nascimento);

        var genero = dadosPaciente.substring(
            dadosPaciente.lastIndexOf("{") + 1, 
            dadosPaciente.lastIndexOf("}")
        );
        j("#edit-genero-"+genero).click();
      });
});