var j = jQuery.noConflict();
var nomeAnterior = '';
j(document).ready(function (){
    j('#autocompleteNomePaciente').on('focusout',function(data){
        if(j(this).val() != nomeAnterior){
            j('input[name ="uid"]').val(' ');
        }
    });
    j('#autocompleteNomePaciente').bind('autocompleteSelect', function(event, node) {
        let str = j('#autocompleteNomePaciente').val();
        nomeAnterior = str;
        var uid = str.substring(
            str.lastIndexOf("(") + 1, 
            str.lastIndexOf(")")
        );
        j('input[name ="uid"]').val(uid);

        let str2 = j('#autocompleteNomePaciente').val();
        var telefone = str2.substring(
            str2.lastIndexOf("<") + 1, 
            str2.lastIndexOf(">")
        );
        j("#edit-telefone").val(telefone);

      });
      
});
  