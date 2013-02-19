<?php
/**
 * Extrai números de stings como 'R$ 3,12' ou '40,2%'
 * 
 */
function extractNumber($str) {
	$multiplier = 1;
	// Verifica qual o formato do número: 2.30 (SI) ou 2,30 (Brasil)
	if(preg_match('/(?:d*.)?d+,/', $str)) {
		$str = str_replace('.','',$str);
		$str = strtr($str,',','.');           
	}
	else{
		$str = str_replace(',','',$str);           
	}
	// Para números negativos
	if(preg_match('/-s*d*.d+/', $str)) {
		$multiplier = -1;
	}
	// Remove tudo o que não for dígito (0 a 9)
	$parts = preg_split('/D/', $str, -1, 
					PREG_SPLIT_DELIM_CAPTURE | 
					PREG_SPLIT_NO_EMPTY);
	// Se houver apenas uma parte, significa que temos um número inteiro
	$number = count($parts) == 1 
				? (int) join('.', $parts)
				: (float) join('.', $parts);
	return   $multiplier;
}

$url = "http://economia.uol.com.br/cotacoes/";
// Recupera o HTML da página em questão
$content = file_get_contents($url);
 
$dom = new DomDocument();
@$dom->loadHTML($content);
 
$xpath = new DOMXPath($dom);
// Realiza uma xQuery buscando pelo elemento .cambio > ul > li > p
$q = $xpath->query('//div[@class="cambio"]/ul/li/p');

$currencies = array();
foreach($q as $n) {
	$children = $n->childNodes;

	$curr = $children->item(0)->nodeValue;
	$variation = $children->item(1)->nodeValue;
	/* Existe um problema na marcação do site da UOL.
	 * Nas cotações existe um espaço entre o elemento contém a
	 * variação e o elemento que contém o valor (confira!).
	 * O problema é que isso é considerado um nó de texto pelo DOM,
	 * E o seu conteúdo, no caso, é uma string vazia.
	 * Caso alterem isso na marcação, o script continuará funcionando
	 * com a lógica abaixo, que verifica se o nó #2 é um elemento.
	 */
	$valueNode = $children->item(2)->noteType == XML_NODE_ELEMENT
					? $children->item(2)
					: $children->item(3);
	$value = $valueNode->nodeValue;
	
	$currencies[$curr] = array();
	$currencies[$curr]['value'] = extractNumber($value);
	$currencies[$curr]['variation'] = extractNumber($variation);
}
var_dump($currencies);

// Realiza uma xQuery buscando pelo elemento .bolsas > ul > li > p
$q = $xpath->query('//div[@class="bolsas"]/ul/li/p');

$stocks = array();
foreach($q as $n) {
	$children = $n->childNodes;

	$stk = $children->item(0)->nodeValue;
	$variation = $children->item(1)->nodeValue;
	$value = $children->item(2)->nodeValue;
	
	$stocks[$stk] = array();
	$stocks[$stk]['value'] = extractNumber($value);
	$stocks[$stk]['variation'] = $variation;
}
$output = json_encode($stocks);
//echo $output;
var_dump($stocks);
echo "<br><br>";
print_r($stocks);
