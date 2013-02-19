<?php include("header.php") ?>
    <title><?php echo $core_nome; ?> - Contato</title>
  </head>
  <body>
    <div id="header">
    <?php include("topo.php"); ?>
    </div>
    
    <div id="content">
    	<div class="centraliza">
        	<div id="falecon" class="pg">
            	<div id="title">
                	<img src="<?php servidor(); ?>images/falecon/title.png" alt="">
                </div>
                <div id="conteudo">
				  Entre em contato com a IS Investimentos para fazer alguma crítica, sugestão ou para mais <br>
				  informações sobre nossos serviços? <br>
				  <br>
				  Você pode mandar um e-mail para contato@isinvestimentos.com.br ou utilize o formulário abaixo <br>
				  para enviar uma mensagem para nossa equipe especializada. <br>
                  <br>
                  Em buscar de uma <strong>oportunidade</strong>? <a href="<?php servidor(); ?>trabalhe-conosco">Clique aqui!</a>
                    <img src="<?php servidor(); ?>images/falecon/contato_numero.png" alt="" style="float:right;margin:35px 60px 0 0px;">
                    <img src="<?php servidor(); ?>images/falecon/icon.jpg" alt="" style="float:right;margin:127px -403px 0 0px;">
                	<form action="" method="post" enctype="multipart/form-data">
	                    <input type="text" name="" id="nome"><br>
	                    <input type="text" name="" id="email"><br>
	                    <input type="text" name="" id="cidade"><br>
	                    <textarea name="" id="mens" cols="30" rows="10"></textarea>
	                    <br><br><br>
	                    <center>
	                    	<input type="image" src="<?php servidor(); ?>images/falecon/enviar_bt.png" alt="" id="cont_bt">
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

