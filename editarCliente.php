<?php
    include_once("./verifica.php");
    include_once("./config/config.php");
    include_once("$base/class/class.cliente.php");
    include_once("$base/class/class.mascara.php");

    $cli = new Cliente();
    $m = new Mascara();

    $dados = $cli->buscaCliente($id_cliente);
    $dado = $dados[0];

    $representante_financeiro_cliente = !empty($dado['cpf_rep_fin']) ? $m->OutMascaraCPF($dado['cpf_rep_fin']).' | '.$dado['nome_rep_fin'] : NULL;


    if ($dado['status_cliente'] == 'ativo') {
        $ativo = 'selected';
        $inativo = '';    
    }
    else{
        $ativo = '';
        $inativo = 'selected';
    }

    include_once 'header.php'; 
?>

    <script type="text/javascript">
        $( document ).ready(function() {
          $("#titulo").html("Editar cliente");
            $('.data').mask('00/00/0000');
            $('.cep').mask('00000-000');
            $('.celular').mask('(00) 00000-0000');
            $('.cpf').mask('000.000.000-00', {reverse: true});
            $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
            $('.cep').mask('99.999-999');

            $('#sexo_cliente').val('<?=$dado['sexo_cliente']?>');

            $('.cep').change(function(){
                var cep = $(this).val();
                cep = cep.replace('.','');
                cep = cep.replace('-','');
                
                $.ajax({
                    url: "https://viacep.com.br/ws/"+cep+"/json/",
                    context: document.body
                }).done(function(resposta) {
                    $('#logradouro_cliente').val(resposta['logradouro']);
                    $('#bairro_cliente').val(resposta['bairro']);
                    $('#cidade_cliente').val(resposta['localidade']);
                    $('#estado_cliente').val(resposta['uf']);
                });       
            });

            $('#salvarAlteracao').click(function(){
                var retorno = true;

                if ($('.campo_obrigatorio').val() == '') {
                    retorno = false;
                    alert('Existem campos obrigatórios não preenchidos!');
                }
                else{
                    $("#form").submit();
                    return true;
                }
            });

            $("#representante_financeiro_cliente").autocomplete({
                source: "./json_busca_cliente.php",
                minLength: 1,
                select: function( event, ui ) {
                    $('#id_representante_financeiro_cliente').val(ui.item.id);
                    $(this).val(ui.item.value);
                }
            });
        });
    </script>
<form action="salvarCliente.php?id_cliente=<?=$id_cliente?>" method='post' name='form' class="" id='form' enctype='multipart/form-data'>
  <div class="row">
    <div class="col-md-3">
      <button class="btn btn-lg btn-primary btn-block" id="salvarAlteracao" type="button">Salvar alteração</button>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-2">
      <div class="form-group text-left">
        <label for="cpf_cliente">CPF <small>(obrigatório)</small></label>
        <input type="text" class="form-control cpf campo_obrigatorio" id="cpf_cliente" name="cpf_cliente" placeholder="CPF do cliente" maxlength="14" required value="<?=$dado[cpf_cliente]?>">
      </div>          
    </div>

    <div class="col-md-5">
      <div class="form-group text-left">
        <label for="nome_cliente">Nome <small>(obrigatório)</small></label>
        <input type="text" class="form-control campo_obrigatorio" id="nome_cliente" name="nome_cliente" placeholder="Nome do cliente" required value="<?=ucwords(strtolower($dado[nome_cliente]))?>">
      </div>          
    </div>

    <div class="col-md-3">
      <div class="form-group text-left">
        <label for="profissao_cliente">Profissão</label>
        <input type="text" class="form-control" id="profissao_cliente" name="profissao_cliente" placeholder="Profissão do cliente" value="<?=ucwords(strtolower($dado[profissao_cliente]))?>">
      </div>          
    </div>

    <div class="col-md-2">
      <div class="form-group text-left">
        <label for="sexo_cliente">Sexo <small>(obrigatório)</small></label>
        <select class="form-control campo_obrigatorio" id="sexo_cliente" name="sexo_cliente">
          <option value="N/I">Não informado</option>
          <option value="F">Feminino</option>
          <option value="M">Masculino</option>
        </select>
      </div>          
    </div>
</div>

<div class="row"> 
    <div class="col-md-3">
      <div class="form-group text-left">
        <label for="data_nascimento_cliente">Data de nascimento <small>(obrigatório)</small></label>
        <input type="text" class="form-control data campo_obrigatorio" id="data_nascimento_cliente" name="data_nascimento_cliente" placeholder="Data de nascimento do cliente" required value="<?=$m->OutMascaraData($dado[data_nascimento_cliente])?>">
      </div>          
    </div>

    <div class="col-md-9">
      <div class="form-group text-left">
        <label for="representante_financeiro_cliente">Nome do responsável financeiro</label>
        <input type="text" class="form-control" id="representante_financeiro_cliente" name="" placeholder="Nome do responsável financeiro" required value="<?=$representante_financeiro_cliente?>">

        <input type="hidden" name="id_representante_financeiro_cliente" class="form-control" id="id_representante_financeiro_cliente" value="<?=$dado[id_rep_fin]?>">

      </div>          
    </div>
</div>

<div class="row">
    <div class="col-md-8">
      <div class="form-group text-left">
        <label for="email_cliente">E-mail</label>
        <input type="email" class="form-control" id="email_cliente" name="email_cliente" placeholder="E-mail do cliente" value="<?=$dado[email_cliente]?>">
      </div>          
    </div>

    <div class="col-md-4">
      <div class="form-group text-left">
        <label for="celular_cliente">Celular <small>(obrigatório)</small></label>
        <input type="text" class="form-control celular campo_obrigatorio" id="celular_cliente" name="celular_cliente" placeholder="Celular do cliente" required value="<?=$dado[celular_cliente]?>">
      </div>          
    </div>
</div>

<div class="row">
    <div class="col-md-2">
      <div class="form-group text-left">
        <label for="cep_cliente">CEP <small>(obrigatório)</small></label>
        <input type="text" class="form-control cep campo_obrigatorio" id="cep_cliente" placeholder="CEP" required value="<?=$dado[cep_cliente]?>" name="cep_cliente">
      </div>          
    </div>

    <div class="col-md-6">
      <div class="form-group text-left">
        <label for="logradouro_cliente">Logradouro <small>(obrigatório)</small></label>
        <input type="text" class="form-control campo_obrigatorio" id="logradouro_cliente" name="logradouro_cliente" placeholder="Logradouro do cliente" required value="<?=$dado[logradouro_cliente]?>">
      </div>          
    </div>

    <div class="col-md-2">
      <div class="form-group text-left">
        <label for="numero_cliente">Número <small>(obrigatório)</small></label>
        <input type="text" class="form-control campo_obrigatorio" id="numero_cliente" name="numero_cliente" placeholder="Número do cliente" value="<?=$dado[numero_cliente]?>">
      </div>          
    </div>

    <div class="col-md-2">
      <div class="form-group text-left">
        <label for="complemento_cliente">Complemento</label>
        <input type="text" class="form-control" id="complemento_cliente" name="complemento_cliente" placeholder="Complemento do cliente" value="<?=$dado[complemento_cliente]?>">
      </div>          
    </div>
</div>

<div class="row">
    <div class="col-md-4">
      <div class="form-group text-left">
        <label for="bairro_cliente">Bairro <small>(obrigatório)</small></label>
        <input type="text" class="form-control campo_obrigatorio" id="bairro_cliente" name="bairro_cliente" placeholder="Bairro do cliente" required value="<?=$dado[bairro_cliente]?>">
      </div>          
    </div>

    <div class="col-md-4">
      <div class="form-group text-left">
        <label for="cidade_cliente">Cidade <small>(obrigatório)</small></label>
        <input type="text" class="form-control campo_obrigatorio" id="cidade_cliente" name="cidade_cliente" placeholder="Cidade do cliente" required value="<?=$dado[cidade_cliente]?>">
      </div>          
    </div>

    <div class="col-md-4">
      <div class="form-group text-left">
        <label for="estado_cliente">Estado <small>(obrigatório)</small></label>
        <input type="text" class="form-control campo_obrigatorio" id="estado_cliente" name="estado_cliente" placeholder="Estado do cliente" required value="<?=$dado[estado_cliente]?>">
      </div>          
    </div>
</div>
<div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label for="status_cliente">Status</label>
        <select class="form-control" id="status_cliente" name="status_cliente">
          <option value="ativo" <?=$ativo?>>Ativo</option>
          <option value="inativo"  <?=$inativo?>>Inativo</option>
        </select>
      </div>          
    </div>
  </div>

</form>
<?= include_once 'footer.php'; ?>
