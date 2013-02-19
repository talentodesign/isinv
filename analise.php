<?php 
include("header.php"); 

//Obtendo e configurando pagina
$pg2802="";
$pg2802=$_GET["pg"];

if($pg2802==""){
	$pg2802="tecnica";
}

//Listando as Páginas
$paginas= array();
$paginas["tecnica"]        ="Analise Técnica";
$paginas["fundamentalista"]="Analise Fundamentalista";
$paginas["setorial"]       ="Analise Setorial";
$paginas["sugerida"]       ="Analise Sugerida";
$paginas["economia"]       ="Economia";

//Redirecionando urls inexistentes
$indice=get_servidor()."analise/";
if(!array_key_exists($pg2802, $paginas)){ 
	echo '<script type="text/javascript">location.href="'.$indice.'";</script>';
	}

//Criando o link para o include
$get_pg="./analise_pgs/".$pg2802.".php";

//Criando indice para pg atual do menu
$ind=array_search($pg2802,array_keys($paginas));
$pg_atual[$ind]='class="atual"';

//Criando Figura do Titulo.
$link_title=get_servidor()."images/analise/".$pg2802."/title.png";
?>
    <title><?php echo $core_nome; ?> - Analise</title>
    <?php 
	?>
  </head>
  <body>
    <div id="header">
    <?php include("topo.php"); ?>
    </div>
    
    <div id="content">
    	<div class="centraliza">
        	<div id="analise" class="pg">
            	<div class="nav">
                	<div id="tit_nav">
                    	HOME :: ANALISE :: <br>
                        <span><?php echo $paginas[$pg2802]; ?> </span>
                    </div>
                    <ul>
                    	<li <?php echo $pg_atual[0]; ?>>
                        	<a href="tecnica"><img src="<?php servidor(); ?>images/analise/menu/tecnica.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[1]; ?>>
                        	<a href="fundamentalista"><img src="<?php servidor(); ?>images/analise/menu/fundam.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[2]; ?>>
                        	<a href="setorial"><img src="<?php servidor(); ?>images/analise/menu/setorial.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[3]; ?>>
                        	<a href="sugerida"><img src="<?php servidor(); ?>images/analise/menu/sugerida.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[4]; ?>>
                        	<a href="economia"><img src="<?php servidor(); ?>images/analise/menu/economia.png" alt=""></a>
                        </li>
                    </ul>
                    <a href=""><img id="sponsor" src="<?php servidor(); ?>images/analise/faca_curs.jpg" alt=""></a><br>
                    <a href=""><img id="sponsor" src="<?php servidor(); ?>images/analise/invista_bol.jpg" alt=""></a>
                    <img src="<?php servidor(); ?>images/analise/icon_s.png" alt="" id="icon_s">
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