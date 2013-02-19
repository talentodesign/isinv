$(document).ready(function(){
	$('.botao').not(".current").hover(
		function(){
			var end= $(this).attr('src');
			var hov = end.replace('_off.','_on.');
			$(this).attr('src', hov);
			},
		function(){
			var end= $(this).attr('src');
			var hov = end.replace('_on.','_off.');
			$(this).attr('src', hov);
			}		  
	);
	$(".current").each(function(){
		var end2= $(this).attr('src');
		var hov2 = end2.replace('_off.','_on.');
		$(this).attr('src', hov2);
		
		});

	/* ------------ CURSOS  AJAX ---------------------*/
	$("#bt_cur").click(function(){
		var curso=$("#curso").val();
		var nome=$("#nome").val();
		var email=$("#email").val();

		var ur=$("#url").val();
		var send=ur+"mail_curso.php"
		var dados="nome="+nome+"&email="+email+"&curso="+curso;
		if(nome =='' || email =='' || curso ==''){
			$('#contato_vazio').modal("show");
			}
		else{
			$('#contato_enviando').modal({backdrop:"static"});
			$('#contato_enviando').modal("show");
			$.ajax({
				type: "POST",
				url: send,
				data: dados,
				cache: false,
				success: function(){
					$("#contato_enviando p").html('<center><img src="'+ur+'images/email-send-icon.png" alt=""></center>').find('center').hide().fadeIn("slow");
					$("#contato_enviando h3").html('Mensagem Enviada');
					setTimeout(some,2000);
				}
				
				});
		}
		return false;
		});
	/* ------------ CONTATO  AJAX ---------------------*/
	$("#contato_enviando").on('hidden',function(){
			$("#contato_enviando p").html('<center><img src="images/loading2.gif" alt=""></center>');
			$("#contato_enviando h3").html('Enviando sua mensagem...');
		});
	var some=function(){$('#contato_enviando').modal('hide')};
	
	$("#falecon #cont_bt").click(function(){
		var nome=$("#nome").val();
		var email=$("#email").val();
		var cidade=$("#cidade").val();
		var mens=$("#mens").val();
		var dados="nome="+nome+"&email="+email+"&cidade="+cidade+"&mens="+mens;
		if(nome =='' || email =='' || cidade =='' || mens==''){
			$('#contato_vazio').modal("show");
			}
		else{
			$('#contato_enviando').modal({backdrop:"static"});
			$('#contato_enviando').modal("show");
			$.ajax({
				type: "POST",
				url: "../mail_contato.php",
				data: dados,
				cache: false,
				success: function(){
					$("#contato_enviando p").html('<center><img src="images/email-send-icon.png" alt=""></center>').find('center').hide().fadeIn("slow");
					$("#contato_enviando h3").html('Mensagem Enviada');
					$("input:text, textarea").val("");
					setTimeout(some,2000);
				}
				
				});
		}
		return false;
		});
	$("#home #cad_bt").click(function(){
		var email=$("#email").val();
		var dados="&email="+email;
		if(email ==''){
			$('#contato_vazio').modal("show");
			}
		else{
			$('#contato_enviando').modal({backdrop:"static"});
			$('#contato_enviando').modal("show");
			$.ajax({
				type: "POST",
				url: "mail_newslet.php",
				data: dados,
				cache: false,
				success: function(){
					$("#contato_enviando p").html('<center><img src="../images/email-send-icon.png" alt=""></center>').find('center').hide().fadeIn("slow");
					$("#contato_enviando h3").html('Mensagem Enviada');
					$("input:text, textarea").val("");
					setTimeout(some,2000);
				}
				
				});
		}
		return false;
	});
	
	
	/* ------------ SLIDE JCYCLE ---------------------*/	
	$("#slider").cycle({fx:"fade"});

	/*------------- COTAÇÃO --------------------------*/
	$("#cotacao").load("yahoostock.php",function(){$('#mq').simplyScroll();})
	
	
	/* ------------ EFEITO SIDEBAR ---------------------*/
	$(".nav ul li").not(".atual").hover(
		function(){
		$(this).stop().animate({'background-color':'#013061'},"slow");
			},
		function(){
		$(this).stop().animate({'background-color':'#032648'},"slow");	
			}
	);
	$('.texto, #scroll').jScrollPane();
	
	/* ------------ TRABALHE CONOSCO AJAX ---------------------*/
	$(".anexo_bt").click(function(){
		$("#anexo").trigger("click");
		return false;
		});
	$("#anexo").change(function(){
		var caminho=$("#anexo").val();
		var arq = caminho.split('\\');
		$("form #arq").val(arq[arq.length-1]);
		});
	$(".enviar_cur").click(function(){
		var nome    = $("#nome").val();
		var email   = $("#email").val();
		if(nome == "" || email == "" || anexo == ""){
			$('#curriculo_vazio').modal("show");
			return false
			}
		});
	
});