<?php 
if(isset($_POST['acao']) && $_POST['acao'] == 'enviar')
	{
		require('trab_contato.php');
	}

include("header.php"); 
if(!isset($enviado)):$enviado="";
endif;

if($enviado){
	?>
	<script type="text/javascript">
	$(document).ready(function(){
    	$('#curriculo_enviado').modal("show");
		});
    </script>
	
	<?php 	
	
	}

?>
    <title><?php echo $core_nome; ?> - Trabalhe Conosco</title>
  </head>
  <body>
    <div id="header">
    <?php include("topo.php"); ?>
    </div>
    
    <div id="content">
    	<div class="centraliza">
        	<div id="trabcon" class="pg">
            	<div id="title">
                	<img src="<?php servidor(); ?>images/trabcon/title.png" alt="">
                </div>
                <div id="conteudo">
				  Cadastre seu currículo em nosso banco de dados. Temos parceria com algumas empresas de recrutamento e seleção.
                	<form action="" method="post" enctype="multipart/form-data">
	                    <input type="text" name="nome" id="nome"><br>
	                    <input type="text" name="email" id="email"><br>
	                    <input type="file" name="anexo"  id="anexo"><br>
	                    <input type="text" disabled name="arq" id="arq"><button class="anexo_bt">ANEXAR</button>
	                    <br><br><br>
	                    <center>
	                    	<input type="hidden" name="acao" value="enviar" />
	                    	<input type="image" src="<?php servidor(); ?>images/falecon/enviar_bt.png" alt="" id="cont_bt" class="enviar_cur">
	                    </center>
                    </form>
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

