 <div class="panel-content">
   <h4 class="titulo">Meus Dados</h4>
   <br>

  <form method="POST">
    <label>Nome</label>
    <input type="text" name="nome" class="form-control" value="<?php echo getDadosUser("nome");?>"><br>

    <label>Usuário</label>
    <input type="text" name="usuario" class="form-control" value="<?php echo getDadosUser("usuario");?>" disabled><br>

    <label>Senha</label>
    <input type="password" name="senha" class="form-control" value="<?php echo getDadosUser("senha");?>"><br>


    <p align="right"><input type="submit" value="Cadastrar" class="btn btn-primary btn-lg btn-block"></p>
    <input type="hidden" name="env" value="alt">
  </form>
  <?php alterarDados();?>
  </div>