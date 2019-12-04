<?php
// load doc header + lines, 4.10.13
header ( "Content-Type: application/json; charset=windows-1251" );
require_once "orasql.php";
require_once "utils.php";

if (isset ( $_REQUEST ['id'] ) && isset ( $_REQUEST ['par'] )) {
	$par = sdecode ( $_REQUEST ['par'] );
	$idDoc = $_REQUEST ['id'];
	try {
		$db = new CMyDb ();
		$db->connect ( $par [0], $par [1], "trn" );
		
		$arlg = explode ( "$", $par [0] );
		$pa = "";
		$msg = "";
		$db->parse ( "begin $arlg[1].BDOC_EX.LIST_BDOC(:cur,:id,:msg,:pa); end;" );
		$db->bind ( ":cur", $cur, OCI_B_CURSOR );
		$db->bind ( ":id", $idDoc, SQLT_INT );
		$db->bind ( ":msg", $msg, SQLT_CHR, 128 );
		$db->bind ( ":pa", $pa, SQLT_CHR, 120 );
		$db->execute ();
		if ($msg)
			throw new Exception ( " $msg" );
		$pars = explode ( ",", $pa );
		$dat = $pars [1];
		if ($dat > 0)
			$dat = "+$dat";
		$dat = strftime ( "%d.%m.%Y", strtotime ( "$dat days" ) );
		$cre = $pars [0] % 10;
		$vkl = floor ( $pars [0] / 10 );
		$db->execute_cursor ( $cur );
		$tab = '';
		$iL = 0;

		echo "[";

		while ( $row = $db->fetch_cursor ( $cur ) ) 
		{
			echo "{";
			$disc = ($row ['PRICE_B'] > 0 ? round ( (1 - $row ['PRICE'] / $row ['PRICE_B']) * 100, 2 ) : 0);
			$maxdisc = ($row ['PRICE'] > 0 ? round ( (1 - $row ['SMP'] / $row ['PRICE']) * 100, 2 ) : 0);
			$maxdiscB = ($row ['PRICE_B'] > 0 ? round ( (1 - $row ['SMP'] / $row ['PRICE_B']) * 100, 2 ) : 0);
			$iL ++;
			//$tab .= "<tr><td>$iL</td>" . "<td ln=$row[ID]>$row[CMC]</td><td ln=$row[ID]>$row[CMC]</td>" . "<td>$row[NAME]</td>" . "<td class='c'>...</dt>"."<td class=kol>$row[KOL]</td>" . "<td class=c>$row[OST]</td>" . "<td class=r>$row[PRICE]</td>" . "<td class=c b='$row[PRICE_B]'>$disc</td>" . "<td class=c v='$row[SMP]' m='$maxdiscB'>$maxdisc</td><td></td>" . "</tr>";

			echo '"ID":"' . $row[ID] . '", ';
			echo '"CMC":"' . $row[CMC] . '", ';
			echo '"NAME":"' . $row[NAME] . '", ';
			echo '"KOL":"' . $row[KOL] . '", ';
			echo '"OST":"' . $row[OST] . '", ';
			echo '"PRICE_ORG":"' . $row[PRICE] . '"},';
		}

		echo "{}]";


		/*$ret_arr = array (
				'tovs' => iconv ( 'cp1251', 'utf-8', $tab ),
				'dt' => $dat,
				'sal' => $pars [4],
				'debt' => $pars [5],
				'lim' => $pars [3],
				'cadr' => $pars [2],
				'cre' => iconv('cp1251', 'utf-8', $cre),
				'vkl' => $vkl,
				'ddt' => $pars [1] 
		);*/
		//echo json_encode ( $ret_arr );




		// echo "{ tovs: \"$tab\", dt: '$dat', sal: '$pars[4]', debt: '$pars[5]',".
		// " lim: '$pars[3]', cadr: '$pars[2]', cre: '$cre', vkl: '$vkl', ddt: '$pars[1]'}";
	} catch ( Exception $e ) {
		echo "{err:" . $e->getMessage () . "}";
	}
}
?>
