<div class="panel-content">
<h4>Bem vindo <b><?php echo getDadosUser("nome");?></b></h4>
<hr>
Aqui algumas estatísticas do blog
<hr>
<div class="container">
  <div class="row">
    <div class="col-sm-3" align="center">
      <h2 class="number"><?php contador_Publicacoes();?></h2>
      <small>Publicações</small>
    </div>

    <div class="col-sm-3" align="center">
      <h2 class="number"><?php contador_Comentarios();?></h2>
      <small>Comentários</small>
    </div>

    <div class="col-sm-3" align="center">
      <h2 class="number"><?php contador_Visualizacoes();?></h2>
      <small>Visualizações</small>
    </div>

    <div class="col-sm-3" align="center">
      <h2 class="number"><?php contador_Administradores();?></h2>
      <small>Administradores</small>
    </div>
  </div>
</div>
<hr>

</div>