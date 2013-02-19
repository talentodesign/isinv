<?php
include_once('class.yahoostock.php');

$objYahooStock = new YahooStock;

/**
	Add format/parameters to be fetched
	
	s = Symbol
	n = Name
	l1 = Last Trade (Price Only)
	d1 = Last Trade Date
	t1 = Last Trade Time
	c = Change and Percent Change
	v = Volume
 */
$objYahooStock->addFormat("sl1c1"); 

/**
	Add company stock code to be fetched
	
	msft = Microsoft
	amzn = Amazon
	yhoo = Yahoo
	goog = Google
	aapl = Apple	
 */
$objYahooStock->addStock("AMBV4.SA");
$objYahooStock->addStock("BBAS3.SA");
$objYahooStock->addStock("BBDC4.SA");
$objYahooStock->addStock("BRFS3.SA");
$objYahooStock->addStock("BVMF3.SA");
$objYahooStock->addStock("CIEL3.SA");
$objYahooStock->addStock("CSNA3.SA");
$objYahooStock->addStock("CYRE3.SA");
$objYahooStock->addStock("GFSA3.SA");
$objYahooStock->addStock("GGBR4.SA");
$objYahooStock->addStock("HYPE3.SA");
$objYahooStock->addStock("ITSA4.SA");
$objYahooStock->addStock("ITUB4.SA");
$objYahooStock->addStock("LREN3.SA");
$objYahooStock->addStock("MMXM3.SA");
$objYahooStock->addStock("MRVE3.SA");
$objYahooStock->addStock("OGXP3.SA");
$objYahooStock->addStock("PDGR3.SA");
$objYahooStock->addStock("PETR4.SA");
$objYahooStock->addStock("RDCD3.SA");
$objYahooStock->addStock("USIM5.SA");
$objYahooStock->addStock("VALE5.SA");

/**
 * Printing out the data

foreach( $objYahooStock->getQuotes() as $code => $stock)
{
	?>
	Code: <?php echo $stock[0]; ?> <br />
	Name: <?php echo $stock[1]; ?> <br />
	Last Trade Price: <?php echo $stock[2]; ?> <br />
	Last Trade Date: <?php echo $stock[3]; ?> <br />
	Last Trade Time: <?php echo $stock[4]; ?> <br />
	Change and Percent Change: <?php echo $stock[5]; ?> <br />
	Volume: <?php echo $stock[6]; ?> <br /><br />
	<?php
}
*/
?>
<ul id="mq">
<?php
foreach( $objYahooStock->getQuotes() as $code => $stock)
{
	$texto=str_replace("\"","",$stock[0]);
	?>
	<li><b><?php echo $texto;//$stock[0]; ?>: </b><?php echo $stock[1]; ?> <?php echo $stock[2]; ?></li>
	<?php
}
?>
</ul>