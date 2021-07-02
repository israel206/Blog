<?php

	function pdo(){
		$db_host = db_host;
		$db_usuario = db_usuario;
		$db_senha = db_senha;
		$db_banco = db_banco;


		try{
			return $pdo = new PDO("mysql:host={$db_host};dbname={$db_banco}", $db_usuario, $db_senha);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			exit("Erro ao conectar-se ao banco: ".$e->getMessage());
		}
	}

	function paginacao(){
		$url = (isset($_GET['pagina'])) ? $_GET['pagina'] : 'inicio';
		$explode = explode('/', $url);
		$dir = 'pags/php/';
		$ext = '.php';

		if(file_exists($dir.$explode[0].$ext)){
			include($dir.$explode[0].$ext);
		}else{
			include($dir."p".$ext);
		}
	}

	function paginacao_adm(){
		$url = (isset($_GET['pagina'])) ? $_GET['pagina'] : 'dashboard';
		$explode = explode('/', $url);
		$dir = 'pags/php/';
		$ext = '.php';

		if(file_exists($dir.$explode[0].$ext) && isset($_SESSION['admLogin'])){
			include($dir.$explode[0].$ext);
		}else{
			include($dir."login".$ext);
		}
	}

	function alerta($tipo, $mensagem){
		echo "<div class='alert alert-{$tipo}'>{$mensagem}</div>";
	}

	function redireciona($tempo, $dir){
		echo "<meta http-equiv='refresh' content='{$tempo}; url={$dir}'>";
	}

	function logIn(){
		if(isset($_POST['log']) && $_POST['log'] == "in"){
			$pdo = pdo();

			$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
			$stmt->execute([':usuario' => $_POST['usuario']]);
			$total = $stmt->rowCount();

			if($total > 0){
				$dados = $stmt->fetch(PDO::FETCH_ASSOC);

				if(password_verify($_POST['senha'], $dados['senha'])){
					$_SESSION['admLogin'] = $dados['usuario'];
					header('Location: dashboard');
				}else{
					alerta("danger", "Usuário ou senha inválidos");
				}
			}
		}
	}

	function verificaLogin(){
		if(isset($_SESSION['admLogin'])){
			header('Location: dashboard');
			exit();
		}
	}

	function getDadosUser($var){
		if(isset($_SESSION['admLogin'])){
			$pdo = pdo();
			$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
			$stmt->execute([':usuario' => $_SESSION['admLogin']]);

			$dados = $stmt->fetch(PDO::FETCH_ASSOC);
			return $dados[$var];
		}
	}

	function get_categorias(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY categoria ASC");
		$stmt->execute();
		$total = $stmt->rowCount();


		if($total > 0){
			while($dados = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "
						<option value='{$dados['id']}'>{$dados['categoria']}</option>
					";

			}
		}else{
			echo alerta("danger", "É necessário ter pelo menos uma categoria antes de criar publicações. Crie uma categoria para continuar.");
			exit();
		}
	}

	function tirarAcentos($string){
	    return strtolower(str_replace(" ","-", preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(Ç)/","/(#)/","/\+/","/\!/","/\?/"),explode(" ","a A e E i I o O u U n N C "),$string)));
	}

	function getData(){
		date_default_timezone_set('America/Sao_Paulo');
		return date('d-m-Y H:i:s');
	}

	function enviarPost(){
		if(isset($_POST['env']) && $_POST['env'] == "post"){
			$pdo = pdo();
			$subtitulo = tirarAcentos($_POST['titulo']);
			$data = getData();

			$uploaddir = '../images/uploads/';
			$uploadfile = $uploaddir.basename($_FILES['userfile']['name']);

			$uploaddir2 = 'images/uploads/';
			$uploadfile2 = $uploaddir2.basename($_FILES['userfile']['name']);

			if($_FILES['userfile']['size'] > 0){
				$stmt = $pdo->prepare("INSERT INTO posts (
				titulo,
				subtitulo,
				postagem,
				imagem,
				data,
				categoria,
				id_postador) VALUES(
				:titulo,
				:subtitulo,
				:postagem,
				:imagem,
				:data,
				:categoria,
				:id_postador
				)
				");
			$stmt->execute([
				':titulo' => $_POST['titulo'],
				':subtitulo' => $subtitulo,
				':postagem' => $_POST['post'],
				':imagem' => $uploadfile2,
				':data' => $data,
				':categoria' => $_POST['categoria'],
				':id_postador' => $_SESSION['admLogin']
			]);

			$total = $stmt->rowCount();

			if($total > 0){
				move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
				alerta("success", "Publicação cadastrada com sucesso!");
			}else{
				alerta("danger", "ERRO AO ENVIAR A PUBLICAÇÃO");
			}
			}else{
				alerta("danger", "INSIRA UMA IMAGEM!");
			}
			
		}
	}

	function getNomeCategoria($id){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT categoria FROM categorias WHERE id = :id");
		$stmt->execute([':id' => $id]);
		$dados = $stmt->fetch(PDO::FETCH_ASSOC);
		return $dados['categoria'];
	}

	function getDadosPost($id, $val){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
		$stmt->execute([':id' => $id]);
		$dados = $stmt->fetch(PDO::FETCH_ASSOC);
		return $dados[$val];
	}

	function getCategoriAtual($id){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = :id");
		$stmt->execute([':id' => $id]);
		$dados = $stmt->fetch(PDO::FETCH_ASSOC);
		echo "<option value='{$dados['id']}'>{$dados['categoria']}(Atual)</option>";
	}

	function getPostsAdmin(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY id DESC");
		$stmt->execute();
		$total = $stmt->rowCount();

		if($total > 0):
			while($dados = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<tr>
  <td>{$dados['id']}</td>
  <td>{$dados['titulo']}</td>
  <td><span class='badge badge-primary'>".getNomeCategoria($dados['categoria'])."</span></td>
  <td>
    <button id='btnGroupDrop1' type='button' class='btn btn-secondary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Gerenciar</button>
    <div class='dropdown-menu' aria-labelledby='btnGroupDrop1'>
      <a class='dropdown-item bg-dark text-light' href='{$dados['subtitulo']}' target='_blank'>Ver Publicação</a>
      <a class='dropdown-item bg-info text-light' href='admin/editar-post/{$dados['id']}'>Editar Publicação</a>
      <a class='dropdown-item bg-danger text-light' href='admin/deletar-post/{$dados['id']}'>Deletar Publicação</a>
    </div>
  </td>
</tr>";
		}
	endif;
	}

	function editarPost($id){
		if(isset($_POST['env']) && $_POST['env'] == "alt"){
			$pdo = pdo();
			$subtitulo = tirarAcentos($_POST['titulo']);

			if($_FILES['userfile']['size'] > 0){
				$uploaddir = '../images/uploads/';
				$uploadfile = $uploaddir.basename($_FILES['userfile']['name']);

				$uploaddir2 = 'images/uploads/';
				$uploadfile2 = $uploaddir2.basename($_FILES['userfile']['name']);

				$stmt = $pdo->prepare("UPDATE posts SET 
													titulo = :titulo,
													subtitulo = :subtitulo,
													postagem = :postagem,
													categoria = :categoria, imagem = :imagem WHERE
													id = :id");
				$stmt->execute([':titulo' => $_POST['titulo'],
								':subtitulo' => $subtitulo,
								':postagem' => $_POST['post'],
								':categoria' => $_POST['categoria'],
								':imagem' => $uploadfile2,
								':id' => $id]);
				move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
			}else{
				$stmt = $pdo->prepare("UPDATE posts SET 
													titulo = :titulo,
													subtitulo = :subtitulo,
													postagem = :postagem,
													categoria = :categoria WHERE
													id = :id");
				$stmt->execute([':titulo' => $_POST['titulo'],
								':subtitulo' => $subtitulo,
								':postagem' => $_POST['post'],
								':categoria' => $_POST['categoria'],
								':id' => $id]);
			}

			$total = $stmt->rowCount();
			

			if($total > 0){
				alerta("success", "Publicação alterada com sucesso!");
				redireciona(2, "admin/editar-post/{$id}");
			}else{
				alerta("danger", "Erro ao alterar");
			}
		}
	}

	function delete($tabela, $coluna, $id, $backpage){
		$pdo = pdo();

		$stmt = $pdo->prepare("DELETE FROM ".$tabela." WHERE ".$coluna." = :id");
		$stmt->execute([':id' => $id]);
		$total = $stmt->rowCount();

		if($total <= 0){
			alerta("danger", "Erro ao deletar");
		}else{
			redireciona(0, $backpage);
		}
	}


	function addCategoria(){
		if(isset($_POST['env']) && $_POST['env'] == "cat"){
			$pdo = pdo();

			$stmt = $pdo->prepare("INSERT INTO categorias (categoria) VALUES (:categoria)");
			$stmt->execute([':categoria' => $_POST['categoria']]);

			$total = $stmt->rowCount();

			if($total > 0){
				alerta("success", "Categoria cadastrada com sucesso!");
			}else{
				alerta("danger", "Erro ao cadastrar categoria");
			}
		}
	}

	function getCategoriasMenu(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY categoria ASC");
		$stmt->execute();

		$total = $stmt->rowCount();

		if($total > 0){
			while($dados = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<li>{$dados['categoria']} <a href='admin/deletar-categoria/{$dados['id']}' class='btn btn-danger btn-sm float-right'>Deletar</a></li>";
			}
		}
	}

	function calculaDias($diaX,$diaY){
		$data1 = new DateTime($diaX);
		$data2 = new DateTime($diaY);

		$intervalo = $data1->diff($data2); 
		if($intervalo->y > 1){ 
		  return $intervalo->y." Anos atrás";
		}elseif($intervalo->y == 1){
		  return $intervalo->y." Ano atrás";
		}elseif($intervalo->m > 1){
		  return $intervalo->m." Meses atrás";
		}elseif($intervalo->m == 1){
		  return $intervalo->m." Mês atrás";
		}elseif($intervalo->d > 1){
		  return $intervalo->d." Dias atrás";
		}elseif($intervalo->d > 0){
		  return $intervalo->d." Dia atrás";
		}elseif($intervalo->h > 0){
		  return $intervalo->h." Horas atrás";
		}elseif($intervalo->i > 1 && $intervalo->i < 59){
		  return $intervalo->i." Minutos atrás";
		}elseif($intervalo->i == 1){
		  return $intervalo->i." Minuto atrás";
		}elseif($intervalo->s < 60 && $intervalo->i <= 0){
		  return $intervalo->s." Segundo atrás";
		}
	}

	function lauchModal($id, $nome, $mensagem){
		echo "<div class='modal fade' id='exampleModalCenter{$id}' tabindex='-1' role='dialog' aria-labelledby='exampleModalCenterTitle' aria-hidden='true'>
	  <div class='modal-dialog modal-dialog-centered' role='document'>
	    <div class='modal-content'>
	      <div class='modal-header'>
	        <h5 class='modal-title' id='exampleModalCenterTitle'>{$nome} Comentou</h5>
	        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
	          <span aria-hidden='true'>&times;</span>
	        </button>
	      </div>
	      <div class='modal-body'>
	        {$mensagem}
	      </div>
    </div>
  </div>
</div>";
	}

	function selecionaComentariosAdm(){
		$pdo = pdo();
		$dataAtual = getData();

		$stmt = $pdo->prepare("SELECT * FROM comentarios ORDER BY id DESC LIMIT 30");
		$stmt->execute();
		$total = $stmt->rowCount();

		if($total > 0){
			while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<tr>
<td>{$dados['id']}</td>
<td>{$dados['nome']}</td>
<td>".calculaDias($dados['data'], $dataAtual)."</td>
<td>
  <button  id='btnGroupDrop1' type='button' class='btn btn-secondary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Gerenciar</button>
  <div class='dropdown-menu' aria-labelledby='btnGroupDrop1'>
    <a class='dropdown-item bg-dark text-light' data-toggle='modal' data-target='#exampleModalCenter{$dados['id']}'>Ver Comentário</a>
    <a class='dropdown-item bg-success text-light' href='{$dados['id_post']}' target='_blank'>Ver Publicação</a>
    <a class='dropdown-item bg-danger text-light' href='admin/deletar-comentario/{$dados['id']}'>Deletar Comentário</a>
  </div>
</td>
</tr>";
lauchModal($dados['id'], $dados['nome'], $dados['comentario']);
			}
		}

	}

	function blockAcesso(){
		if(getDadosUser("superadmin") != 1){
			redireciona(0, "admin/dashboard");
			exit();
		}	
	}

	function cadastrarAdm(){
		if(isset($_POST['env']) && $_POST['env'] == "adm"){
			$pdo = pdo();
			$senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);

			$statusCheckBox;

			if(isset($_POST['superadmin'])){
				$statusCheckBox = 1;
			}else{
				$statusCheckBox = 0;
			}

			$stmt = $pdo->prepare("INSERT INTO usuarios (nome, usuario, senha, superadmin) VALUES(:nome, :usuario, :senha, :superadmin)");
			$stmt->execute([
				':nome' => $_POST['nome'],
				':usuario' => $_POST['usuario'],
				':senha' => $senha,
				':superadmin' => $statusCheckBox
				]);

			$total = $stmt->rowCount();

			if($total > 0){
				alerta("success", "Administrador cadastrado com sucesso!");
			}else{
				alerta("danger", "Erro ao adicionar!");
			}
		}
	}

	function listaAdministradores(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM usuarios ORDER BY nome ASC");
		$stmt->execute();

		$total = $stmt->rowCount();

		if($total > 0){
			while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<li>{$dados['nome']} <a href='admin/deletar-administrador/{$dados['id']}' class='btn btn-danger btn-sm float-right'>Deletar</a></li>";
			}
		}
	}


	function alterarDados(){
		if(isset($_POST['env']) && $_POST['env'] == "alt"){
			$pdo = pdo();

			$nSenha;

			if($_POST['senha'] == getDadosUser("senha")){
				$nSenha = $_POST['senha'];
			}else{
				$nSenha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
			}

			$stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, senha = :senha WHERE usuario = :usuario");
			$stmt->execute([
				':nome' => $_POST['nome'],
				':senha' => $nSenha,
				':usuario' => $_SESSION['admLogin'] 
			]);

			$total = $stmt->rowCount();

			if($total > 0){
				alerta("success", "Dados alterado com sucesso!");
				redireciona(3, "admin/me");
			}else{
				alerta("danger", "Erro ao alterar");
			}
		}
	}

	function logout(){
		session_destroy();
		redireciona(2, "admin/login");
	}

	function contador_Publicacoes(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT id FROM posts");
		$stmt->execute();

		echo $stmt->rowCount();
	}

	function contador_Comentarios(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT id FROM comentarios");
		$stmt->execute();

		echo $stmt->rowCount();
	}

	function contador_ComentariosFromPost($id_post){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT id FROM comentarios WHERE id_post = :id_post");
		$stmt->execute([':id_post' => $id_post]);

		return $stmt->rowCount();
	}

	function contador_Visualizacoes(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT SUM(visualizacoes) as soma FROM posts");
		$stmt->execute();
		$dados = $stmt->fetch(PDO::FETCH_ASSOC);

		echo $dados['soma'];
	}

	function contador_VisualizacoesFromPosts($id){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT visualizacoes FROM posts WHERE id = :id");
		$stmt->execute([':id' => $id]);
		$dados = $stmt->fetch(PDO::FETCH_ASSOC);

		return $dados['visualizacoes'];
	}

	function contador_Administradores(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT id FROM usuarios");
		$stmt->execute();

		echo $stmt->rowCount();
	}

	function limitaCaracters($titulo){
		if(strlen($titulo) <= totalCaractersPosts){
			return $titulo;
		}else{
			return mb_substr($titulo, 0, totalCaractersPosts)."...";
		}
	}

	function getDadosUserSite($usuario, $var){
			$pdo = pdo();
			$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
			$stmt->execute([':usuario' => $usuario]);

			$dados = $stmt->fetch(PDO::FETCH_ASSOC);
			return $dados[$var];
	}

	function getPosts(){
		$pdo = pdo();
		$data = getData();

		$url = (isset($_GET['pagina'])) ? $_GET['pagina'] : 'dashboard';
		$explode = explode('/', $url);
		$pg = (isset($explode['2'])) ? $explode['2'] : 1;
		$maximo = totalPostsPorPagina;
		$inicio = ($pg -1) * $maximo;

		$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY id DESC limit $inicio,$maximo");
		$stmt->execute();

		$total = $stmt->rowCount();

		if($total > 0){
			while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {

				echo "<div class='content-post'>
  <div class='title'>
    <a href='{$dados['subtitulo']}'>{$dados['titulo']}</a> 
  </div>
  <div class='content'>
    <div class='container'>
      <div class='row'>
        <div class='col-sm-3'>
          <img src='{$dados['imagem']}' class='img-fluid'>
        </div>
        <div class='col-sm'>
         ".limitaCaracters(strip_tags($dados['postagem']))." 
        </div>
      </div>
  <div class='infos'>
    <i class='fas fa-user'></i> ".getDadosUserSite($dados['id_postador'],"nome")." |
    <i class='fas fa-tag'></i> <a href='categoria/{$dados['categoria']}' class='badge badge-primary'>".getNomeCategoria($dados['categoria'])."</a> |
    <i class='fas fa-eye'></i> ".$dados['visualizacoes']." Visitas |  
    <i class='fas fa-comment'></i> ".contador_ComentariosFromPost($dados['id'])." Comentários |
    <i class='far fa-clock'></i> ".calculaDias($data, $dados['data'])."
  </div>
    </div>
  </div>
</div>";
			}
		}
	}

	function listaPaginas(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM posts");
		$stmt->execute();
		$total = $stmt->rowCount();

		$maximo = totalPostsPorPagina;
		$links = ceil($total/$maximo);
		$pg = (isset($explode['2'])) ? $explode['2'] : 1;

		for($i = 1; $i < $pg + $links; $i++){
			echo "<li class='page-item'><a class='page-link' href='inicio/posts/{$i}'>{$i}</a></li>";
		}
	}

	function listaPaginasFromCategoria($categoria){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM posts WHERE categoria = :categoria");
		$stmt->execute([':categoria' => $categoria]);
		$total = $stmt->rowCount();

		$maximo = totalPostsPorPagina;
		$links = ceil($total/$maximo);
		$pg = (isset($explode['2'])) ? $explode['2'] : 1;

		for($i = 1; $i < $pg + $links; $i++){
			echo "<li class='page-item'><a class='page-link' href='categoria/{$categoria}/posts/{$i}'>{$i}</a></li>";
		}
	}


	function getPopularesPosts(){
		$pdo = pdo();
		$limite = totalPostsPorPagina;

		$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY visualizacoes DESC LIMIT $limite");
		$stmt->execute();
		$total = $stmt->rowCount();

		if($total > 0){
			while($dados = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<div class='content margin-top'>
	<div class='media'>
	  <img src='{$dados['imagem']}' class='mr-3'>
	  <div class='media-body'>
	    <h5 class='mt-0'><a href='{$dados['subtitulo']}'>{$dados['titulo']}</a></h5>
	  </div>
	</div>
</div>";
			}
		}
	}

	function getTotalPostsByCategoria($categoria){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM posts WHERE categoria = :categoria");
		$stmt->execute([':categoria' => $categoria]);
		$total = $stmt->rowCount();

		if($total > 0){
			return "(".$total.")";
		}
	}

	function getCategoriasSite(){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY categoria ASC");
		$stmt->execute();
		$total = $stmt->rowCount();

		if($total > 0){
			while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "<li><a href='categoria/{$dados['id']}'>{$dados['categoria']} ".getTotalPostsByCategoria($dados['id'])."</a></li>";
			}
		}
	}


	function getPostsFromCategoria($categoria){
		$pdo = pdo();
		$data = getData();

		$url = (isset($_GET['pagina'])) ? $_GET['pagina'] : 'dashboard';
		$explode = explode('/', $url);
		$pg = (isset($explode['3'])) ? $explode['3'] : 1;
		$maximo = totalPostsPorPagina;
		$inicio = ($pg -1) * $maximo;

		$stmt = $pdo->prepare("SELECT * FROM posts WHERE categoria = :categoria ORDER BY id DESC limit $inicio,$maximo");
		$stmt->execute([':categoria' => $categoria]);

		$total = $stmt->rowCount();

		if($total > 0){
			while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {

				echo "<div class='content-post'>
  <div class='title'>
    <a href='{$dados['subtitulo']}'>{$dados['titulo']}</a> 
  </div>
  <div class='content'>
    <div class='container'>
      <div class='row'>
        <div class='col-sm-3'>
          <img src='{$dados['imagem']}' class='img-fluid'>
        </div>
        <div class='col-sm'>
         ".limitaCaracters(strip_tags($dados['postagem']))." 
        </div>
      </div>
  <div class='infos'>
    <i class='fas fa-user'></i> ".getDadosUserSite($dados['id_postador'],"nome")." |
    <i class='fas fa-tag'></i> <a href='categoria/{$dados['categoria']}' class='badge badge-primary'>".getNomeCategoria($dados['categoria'])."</a> |
    <i class='fas fa-eye'></i> ".$dados['visualizacoes']." Visitas |  
    <i class='fas fa-comment'></i> ".contador_ComentariosFromPost($dados['id'])." Comentários |
    <i class='far fa-clock'></i> ".calculaDias($data, $dados['data'])."
  </div>
    </div>
  </div>
</div>";
			}
		}
	}

	function getPostsFromBusca(){
		$pdo = pdo();
		$data = getData();

		if(isset($_POST['busca'])){
			$busca = "%{$_POST['busca']}%";
		}else{
			$busca = "";
		}
		

		$stmt = $pdo->prepare("SELECT * FROM posts WHERE titulo LIKE :titulo OR postagem LIKE :postagem ORDER BY id DESC ");
		$stmt->execute([':titulo' => $busca, ':postagem' => $busca]);

		$total = $stmt->rowCount();

		if($total > 0){
			while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {

				echo "<div class='content-post'>
  <div class='title'>
    <a href='{$dados['subtitulo']}'>{$dados['titulo']}</a> 
  </div>
  <div class='content'>
    <div class='container'>
      <div class='row'>
        <div class='col-sm-3'>
          <img src='{$dados['imagem']}' class='img-fluid'>
        </div>
        <div class='col-sm'>
         ".limitaCaracters(strip_tags($dados['postagem']))." 
        </div>
      </div>
  <div class='infos'>
    <i class='fas fa-user'></i> ".getDadosUserSite($dados['id_postador'],"nome")." |
    <i class='fas fa-tag'></i> <a href='categoria/{$dados['categoria']}' class='badge badge-primary'>".getNomeCategoria($dados['categoria'])."</a> |
    <i class='fas fa-eye'></i> ".$dados['visualizacoes']." Visitas |  
    <i class='fas fa-comment'></i> ".contador_ComentariosFromPost($dados['id'])." Comentários |
    <i class='far fa-clock'></i> ".calculaDias($data, $dados['data'])." 
  </div>
    </div>
  </div>
</div>";
			}
		}
	}


	function getCompletePost(){
		$url = (isset($_GET['pagina'])) ? $_GET['pagina'] : 'inicio';
		$explode = explode('/', $url);

		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT * FROM posts WHERE subtitulo = :subtitulo");
		$stmt->execute([':subtitulo' => $explode[0]]);
		$total = $stmt->rowCount();

		if($total <=0){
			include("pags/php/404.php");
			exit();
		}else{
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
	}

	function enviarComentario($id_post, $subtitulo){
		if(isset($_POST['env']) && $_POST['env'] == "comentario"){
			$pdo = pdo();
			$data = getData();

			$stmt = $pdo->prepare("INSERT INTO comentarios (id_post, nome, comentario, data) VALUES (:id_post, :nome, :comentario, :data)");
			$stmt->execute([':id_post' => $id_post,
							':nome' => $_POST['nome'],
							':comentario' => $_POST['comentario'],
							':data' => $data]);
			$total = $stmt->rowCount();

			if($total > 0){
				alerta("success", "Comentário enviado com sucesso!");
				redireciona(3, $subtitulo."#comentarios");
			}else{
				alerta("danger", "Erro ao enviar o comentário");
			}
		}
	}

	function pegaComentariosPost($id_post){
		$pdo = pdo();
		$data = getData();

		$stmt = $pdo->prepare("SELECT * FROM comentarios WHERE id_post = :id_post ORDER BY id DESC");
		$stmt->execute([':id_post' => $id_post]);
		$stmt->execute();

		$total = $stmt->rowCount();

		if($total > 0){
			while($dados = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<div class='container'>
  <div class='row'>
    <div class='col-sm-2'>
      <img src='images/template/nophoto.png' class='img-fluid'>
    </div>
    <div class='col-sm'>
      <div class='card'>
        <div class='card-header'>
          <b>{$dados['nome']}</b> Comentou 
          <span class='float-right small'>".calculaDias($data, $dados['data'])."</span>
        </div>
        <div class='card-body'>
          {$dados['comentario']}
        </div>
      </div>
    </div>
  </div>
</div><br>";
			}
		}
	}

	function deletaFoto($id_post){
		$pdo = pdo();

		$stmt = $pdo->prepare("SELECT imagem FROM posts WHERE id = :id");
		$stmt->execute([':id' => $id_post]);
		$total = $stmt->rowCount();

		if($total > 0){
			$dados = $stmt->fetch(PDO::FETCH_ASSOC);
			unlink("../{$dados['imagem']}") or die("Erro ao deletar imagem");
		}
	}

	function geraTitulo($titulo){
		$url = (isset($_GET['pagina'])) ? $_GET['pagina'] : 'inicio';
		$explode = explode('/', $url);

		switch($explode['0']){
			case 'inicio':
			echo $titulo." | Inicio";
			break;

			case 'sobre':
			echo $titulo." | Sobre";
			break;

			case 'contato':
			echo $titulo." | Contato";
			break;

			case 'categoria':
			echo "Buscando na categoria: ".strtoupper(getNomeCategoria($explode['1']));
			break;

			case 'busca':
			echo "Buscando por: ".strtoupper($_POST['busca']);
			break;

			case '404':
			echo "ERROR 404";
			break;

			default:
			$dados = getCompletePost($explode[0]);
			echo $dados['titulo'];
			break;
		}
	}

	function atualizaVisitas($id_post){
		$nVisitas =  contador_VisualizacoesFromPosts($id_post) + 1;

		$pdo = pdo();

		$stmt = $pdo->prepare("UPDATE posts SET visualizacoes = :visualizacoes WHERE id = :id");
		$stmt->execute([':visualizacoes' => $nVisitas, ':id' => $id_post]);

		//$total = $stmt->rowCount();
	}
?>
