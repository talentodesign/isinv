<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<title>Untitled Document</title>
</head>

<body>
Inicial<input type="text" name="" id="inicial"><br>
Mensal<input type="text" name="" id="mensal"><br>
Tempo<input type="text" name="" id="tempo"><br>
Rentabilidade<input type="text" name="" id="rentabilidade"><br>
<button value="Calcular" id="calc">Calcular</button>
<br>
<br>
<div></div>

</body>
<script type="text/javascript">
$.noConflict();
	function replaceAll(str, de, para){
	    var pos = str.indexOf(de);
	    while (pos > -1){
	        str = str.replace(de, para);
	        pos = str.indexOf(de);
	    }
	    return (str);
	}
	
	function converterStringFloat(string){
	     string = replaceAll(string, ".", "");
	     var float = parseFloat(replaceAll(string, ",", "."));
	
	     return float;
	}
	
	function calcularRentabilidade(aplicacaoInicial, aplicacaoMensal, tempo, rentabilidade){
	
	    var ai = aplicacaoInicial;
	    var am = aplicacaoMensal;
	    var r = rentabilidade / 100.0;
	
	    var resultado = ((am - (Math.pow( (1 + r),(tempo)) ) * ((ai * r) + am)) / r ) * (-1);
	
	    return resultado.toFixed(2);
	    
	function float2moeda(num) {
	
	       x = 0;
	
	       if(num<0) {
	          num = Math.abs(num);
	          x = 1;
	       }
	       if(isNaN(num)) num = "0";
	          cents = Math.floor((num*100+0.5)%100);
	
	       num = Math.floor((num*100+0.5)/100).toString();
	
	       if(cents < 10) cents = "0" + cents;
	          for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	             num = num.substring(0,num.length-(4*i+3))+'.'
	                   +num.substring(num.length-(4*i+3));
	       ret = num + ',' + cents;
	       if (x == 1) ret = ' - ' + ret;return ret;
	
		}
	}
	jQuery("#calc").click(function(){
		var aplicacaoInicial = converterStringFloat(jQuery("#inicial").val());
		var aplicacaoMensal  = converterStringFloat(jQuery("#mensal").val());
		var tempo            = jQuery("#mensal").val();
		
		var aporteTotal = aplicacaoInicial + (tempo * aplicacaoMensal);
		var total= float2moeda(aporteTotal);		    
		    
		
		jQuery("div").html("Aporte Total: "+total);
	});

</script>
</html>