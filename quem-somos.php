<?php 
include("header.php"); 

//Obtendo e configurando pagina
$pg2802="";
$pg2802=$_GET["pg"];

if($pg2802==""){
	$pg2802="a-empresa";
}

//Listando as Páginas
$paginas= array();
$paginas["a-empresa"]        ="A Empresa";
$paginas["precos"]           ="Precos";
$paginas["imp-mercado"]      ="A Importância do Mercado";
$paginas["acoes"]          	 ="Ações como formação de...";
$paginas["diversifique"]     ="Diversifique os investimentos";
$paginas["fotos"]            ="Fotos";
$paginas["equipe"]           ="equipe";

//Redirecionando urls inexistentes
$indice=get_servidor()."quem-somos/";
if(!array_key_exists($pg2802, $paginas)){ 
	echo '<script type="text/javascript">location.href="'.$indice.'";</script>';
	}

//Criando o link para o include
$get_pg="./quems_pgs/".$pg2802.".php";

//Criando indice para pg atual do menu
$ind=array_search($pg2802,array_keys($paginas));
$pg_atual[$ind]='class="atual"';

//Criando Figura do Titulo.
$link_title=get_servidor()."images/quem-somos/".$pg2802."/title.png";
?>
    <title><?php echo $core_nome; ?> - Quem Somos</title>
    <?php 
	?>
  </head>
  <body>
    <div id="header">
    <?php include("topo.php"); ?>
    </div>
    
    <div id="content">
    	<div class="centraliza">
        	<div id="quems" class="pg">
            	<div class="nav">
                	<div id="tit_nav">
                    	HOME :: QUEM SOMOS :: <br>
                        <span><?php echo $paginas[$pg2802]; ?> </span>
                    </div>
                    <ul>
                    	<li <?php echo $pg_atual[0]; ?>>
                        	<a href="a-empresa"><img src="<?php servidor(); ?>images/quem-somos/menu/empresa.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[1]; ?>>
                        	<a href="precos"><img src="<?php servidor(); ?>images/quem-somos/menu/precos.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[2]; ?>>
                        	<a href="imp-mercado"><img src="<?php servidor(); ?>images/quem-somos/menu/imf.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[3]; ?>>
                        	<a href="acoes"><img src="<?php servidor(); ?>images/quem-somos/menu/acoes.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[4]; ?>>
                        	<a href="diversifique"><img src="<?php servidor(); ?>images/quem-somos/menu/diversifique.png" alt=""></a>
                        </li>
                    	<li <?php echo $pg_atual[5]; ?>>
                        	<a href="fotos"><img src="<?php servidor(); ?>images/quem-somos/menu/fotos.png" alt=""></a>
                        </li>
                        <li <?php echo $pg_atual[6]; ?>>
                            <a href="equipe"><img src="<?php servidor(); ?>images/quem-somos/menu/equipe.png" alt=""></a>
                        </li>
                     </ul>
                    
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