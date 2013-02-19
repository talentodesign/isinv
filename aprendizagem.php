<?php 
include("header.php"); 

//Obtendo e configurando pagina
$pg2802="";
$pg2802=$_GET["pg"];

if($pg2802==""){
	$pg2802="cef";
}

//Listando as Páginas
$paginas= array();
$paginas["cef"]="Canal de Educação Financeira";
$paginas["descubra-seu-perfil"]="Descubra o seu perfil";
$paginas["simuladores"]="Simuladores";
$paginas["mercado-a-vista"]="Mercado A Vista";
$paginas["cursos"]="Cursos";
$paginas["bdc"]="Banco de Conhecimento";
$paginas["glossario-da-bolsa"]="Glossário da Bolsa";
$paginas["mercado-de-acoes"]="Mercado de Ações";
$paginas["opcoes"]="Opções";

//Redirecionando urls inexistentes
$indice=get_servidor()."aprendizagem/";
if(!array_key_exists($pg2802, $paginas)){ 
	echo '<script type="text/javascript">location.href="'.$indice.'";</script>';
	}

//Criando o link para o include
$get_pg="./aprend_pgs/".$pg2802.".php";

//Criando indice para pg atual do menu
$ind=array_search($pg2802,array_keys($paginas));
$pg_atual[$ind]='class="atual"';

//Criando Figura do Titulo.
$link_title=get_servidor()."images/aprendizagem/".$pg2802."/title.png";
?>
    <title><?php echo $core_nome; ?> - Aprendizagem</title>
    <?php 
	?>
  </head>
  <body>
    <div id="header">
    <?php include("topo.php"); ?>
    </div>
    
    <div id="content">
    	<div class="centraliza">
        	<div id="aprendizagem" class="pg">
            	<div class="nav">
                	<div id="tit_nav">
                    	HOME :: APRENDIZAGEM :: <br>
                        <span><?php echo $paginas[$pg2802]; ?> </span>
                    </div>
                    <ul>
                    	<li <?php echo $pg_atual[0]; ?>>
                        	<a href="cef"><img src="<?php servidor(); ?>images/aprendizagem/menu/ced.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[1]; ?>>
                        	<a href="descubra-seu-perfil"><img src="<?php servidor(); ?>images/aprendizagem/menu/d_perfil.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[2]; ?>>
                        	<a href="simuladores"><img src="<?php servidor(); ?>images/aprendizagem/menu/simulad.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[3]; ?>>
                        	<a href="mercado-a-vista"><img src="<?php servidor(); ?>images/aprendizagem/menu/mercado_av.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[4]; ?>>
                        	<a href="cursos"><img src="<?php servidor(); ?>images/aprendizagem/menu/cursos.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[5]; ?>>
                        	<a href="bdc"><img src="<?php servidor(); ?>images/aprendizagem/menu/base_con.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[6]; ?>>
                        	<a href="glossario-da-bolsa"><img src="<?php servidor(); ?>images/aprendizagem/menu/glossario.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[7]; ?>>
                        	<a href="mercado-de-acoes"><img src="<?php servidor(); ?>images/aprendizagem/menu/mercado_ac.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[8]; ?>>
                        	<a href="opcoes"><img src="<?php servidor(); ?>images/aprendizagem/menu/opcoes.png" alt=""></a>
                        </li>
                    </ul>
                    <a href=""><img id="sponsor" src="<?php servidor(); ?>images/aprendizagem/abracont_banner.jpg" alt=""></a>
                    <img src="<?php servidor(); ?>images/aprendizagem/icon_s.png" alt="" id="icon_s">
                </div>
                <div class="conteudo">
                	<div id="title_c">
                    	<img src="<?php echo $link_title; ?>" alt="">
                    </div>
                    <div class="texto">
                    <?php include($get_pg); ?>	
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