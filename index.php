<?php 
include("header.php");
wp_funcoes();
 ?>
    <title><?php echo $core_nome; ?></title>
  </head>
  <body>
    <div id="header">
    <?php include("topo.php"); ?>
    </div>
    <div id="cotacao" class="centraliza">
        <div class="centraliza" style="text-align:center">Carregando...</div>
        <div class="separator"></div>
    </div>
    <div id="slider_area">
    	<div id="sl" class="centraliza">
        	<div id="slider">
            	<a href=""><img src="<?php servidor(); ?>images/slider_home/lib_vida_tranq.jpg" alt=""></a>
            	<a href=""><img src="<?php servidor(); ?>images/slider_home/futuro_familia.jpg" alt=""></a>
            	<a href=""><img src="<?php servidor(); ?>images/slider_home/inv_ferram.jpg" alt=""></a>
            </div>
        </div>    
    </div>
    <div id="content">
    	<div id="home" class="centraliza">
        	<div id="calendario">
            	<ul>
                <?php query_posts('posts_per_page=3&post_type=pronamic_event'); ?>
                <?php while (have_posts()): the_post(); ?>
            		<li>
                    	<a href="<?php servidor(); ?>calendario/p/<?php echo $post->post_name; ?>"><strong><?php echo pronamic_get_the_start_date('d/m');?></strong> - <?php the_title(); ?></a>
                    </li>
                <?php endwhile; ?>
                <?php wp_reset_query();?>
            	</ul>
                <center>&gt;&gt; <a href="<?php servidor(); ?>calendario/">ver todos</a></center>
            </div>
            <a href=href="https://www.agorainvest.com.br/bemvindo/abraconta/cadastro.asp?cliente=M&Cd_Assessor=427" target="_blank"><img src="<?php servidor(); ?>images/bg_abracont.png" alt="" id="abracont"></a>
            <div id="noticias">
            	<ul>
            <?php  
				query_posts( array( 'posts_per_page'=> 2, 'paged' => 1 ) );
				while (have_posts()): the_post();
			?>
            		<li>
                    	<a href="<?php servidor(); ?>noticias/p/<?php echo $post->post_name; ?>"><?php the_title(); ?></a><br>
                        <?php echo get_the_date('d/m/Y'); ?>	
                    </li>
             <?php endwhile; ?>	
            	</ul>
                <div style="text-align:right">&gt;&gt; <a href="<?php servidor(); ?>noticias/">ver todos</a></div>
            </div>
            <!--<a href=""><img src="images/bg_chat.png" alt="" id="chat"></a> -->
            <div id="chat">
            	<!-- LiveZilla Chat Button Link Code (ALWAYS PLACE IN BODY ELEMENT) -->
                <div style="text-align:center;width:208px;"><a href="javascript:void(window.open('http://isinvestimentos.com.br/chat/chat.php','','width=590,height=610,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes'))"><img src="http://isinvestimentos.com.br/chat/image.php?id=04&amp;type=inlay" width="208" height="191" border="0" alt="LiveZilla Live Help"></a></div><!-- http://www.LiveZilla.net Chat Button Link Code --><!-- LiveZilla Tracking Code (ALWAYS PLACE IN BODY ELEMENT) --><div id="livezilla_tracking" style="display:none"></div><script type="text/javascript">
var script = document.createElement("script");script.type="text/javascript";var src = "http://isinvestimentos.com.br/chat/server.php?request=track&output=jcrpt&nse="+Math.random();setTimeout("script.src=src;document.getElementById('livezilla_tracking').appendChild(script)",1);</script><noscript><img src="http://isinvestimentos.com.br/chat/server.php?request=track&amp;output=nojcrpt" width="0" height="0" style="visibility:hidden;" alt=""></noscript><!-- http://www.LiveZilla.net Tracking Code -->
            </div>
            <img src="images/p_title.png" alt="" id="p_title">
            <div id="parceiros">
                <a href="http://www.thomaz.com.br/" target="_blank"><img src="<?php servidor(); ?>images/distribuidores/thomaz-corretora-de-seguros.png" alt=""></a>
            </div>
            <div id="cotacao">
                <br><br>
                <label for="">
                    Gostaria de receber <br>
                    informações no seu e-mail?
                </label><br><br>
                <label for="email">
                    INSCREVA-SE
                </label><br>
                <input type="text" name="" id="email" placeholder="Digite seu email" ><br>
                <input type="image" src="<?php servidor(); ?>images/cad_bt_on.png" alt="" id="cad_bt">

            </div>
        </div>
    </div>
    <div id="footer">
    <?php include("footer.php"); ?>
    <?php include("contato_msg.php"); ?>
    </div>
  </body>
</html>

