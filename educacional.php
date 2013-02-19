<?php 
include("header.php");
wp_funcoes();
$pg="";
$pg=$_GET["pg"];
if($pg==""):$pg=1;
endif;

$p="";
$p=$_GET["p"];
$qry="name=".$p."&post_type=cursos";

//Descobrindo o numero de posts
$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'cursos'");
if (0 < $numposts) $numposts = number_format($numposts); 

$paginacao = new Paginacao;          //Criando Objeto para paginação, em seguida instanciamos...
$paginacao->set_numposts($numposts); //... o número total de posts.
$paginacao->set_porpg(6);            //... o número de posts por página.
$paginacao->set_pg_atual($pg);   //... a pagina atual.

$serv= get_servidor(); //Pegando o endereço do servidor
$end_ant  = $serv."educacional/pg/".$paginacao->antpg();   //Criando o endereco de pagina anterior
$end_prox = $serv."educacional/pg/".$paginacao->proxpg();  //Criando o endereco de pagina posterior
?>
    <title><?php echo $core_nome; ?> - Educacional</title>
  </head>
  <body>
    <div id="header">
    <?php include("topo.php"); ?>
    </div>
    
    <div id="content">
    	<div class="centraliza">
        	<div id="noticias" class="pg">
            	<div id="title">
                	<img src="<?php servidor(); ?>images/educacional/title.png" alt="">
                </div>
                <div id="conteudo">
                	<ul id="indice">
                    <?php  
						query_posts( array( 'post_type'=> 'cursos','posts_per_page'=> 6, 'paged' => $pg ) );
						while (have_posts()): the_post();
					?>
                    	<a href="<?php servidor(); ?>educacional/p/<?php echo $post->post_name; ?>">
	                        <li>
	                        	<span><?php echo get_the_date('d M');?></span><br>
	                            <?php the_title(); ?>
	                        </li>
                        </a>
                     <?php 
	  					 endwhile; 
	  					 wp_reset_query(); 
					 ?>
                     <li class="pag">
                     	<?php if($paginacao->has_anterior()){ ?>
							<a href="<?php echo $end_ant; ?>">Anterior</a>
						<?php } ?>
                        
                     	<?php if($paginacao->has_proximo()){ ?>
							<a href="<?php echo $end_prox; ?>">Próximo</a>
						<?php } ?>
                     	
                     </li>
                    </ul>
                    <div id="noticia">
                    <?php if($p!=""){ ?>
                    <?php query_posts($qry);?>
                    <?php }else{query_posts('posts_per_page=1&post_type=cursos');} ?>
					<?php while (have_posts()): the_post();?>
                    	<div id="data">
                        	<?php echo get_the_date('d M');?>
                        </div>
                        <div id="texto">
                        	<div id="scroll">
                            <p id="titulo"><?php echo get_the_title(); ?></p>
	                          <?php the_content(); ?> <br>
                            <p>
                              <fieldset>
                                <legend><strong>INSCREVA-SE</strong></legend>
                                <input type="text" name="" id="curso" value="<?php echo get_the_title(); ?>" disabled><br><br>
                                <label for="nome">Nome</label><br>
                                <input type="text" name="nome" id="nome" placeholder="Digite seu nome completo"><br>
                                <label for="">Email</label><br>
                                <input type="text" name="" id="email" placeholder="Digite seu email de contato" ><br><br>
                                <input type="hidden" name="url" value="<?php servidor(); ?>" id="url">
                                <input type="image" src="<?php servidor(); ?>images/cad_bt_on.png" alt="" id="bt_cur">
                              </fieldset>
                            </p>
                            </div>
                        </div>
                     <?php endwhile;?>
                    </div>
                </div>
                
            </div>
            <div class="separator"></div>
        </div>
    </div>
    <div id="footer">
    <?php include("footer.php"); ?>
    <?php include("contato_msg.php"); ?>
    </div>
  </body>
</html>

