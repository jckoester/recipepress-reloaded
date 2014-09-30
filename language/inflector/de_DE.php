<?php
/*
 * Created on 15.10.2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$uncountables=array (
	'Becher',
	'Blatt',
	'Bund',
	'cl',
	'cm',
	'EL',
	'etwas',
	'g',
	'Handvoll',
	'Kästchen',
	'ml',
	'Paar',
	'Stück',
	'TL',
);
        
$irregulars=array (
    'Blech' => 'Bleche',
	'Bund' => 'Bund',
	'Ei'=>'Eier',
	//	'EL'=>'EL',
	'Glas' => 'Gläser',
	'Kapsel'=>'Kapseln',
	'Portion'=>'Portionen',
	//'Stück' => 'Stück',
);

function regular_plural($str){
	if ( preg_match('/[e]$/', $str) ) {
		//Singular auf -e hat Plural -n
		$str .= 'n';
	} elseif (preg_match('/[aiou]$/', $str)) {
		//Singualr auf -a, -i, -o oder -u hat Plural auf -s
        $str .= 's';
	} elseif(preg_match('/e[lnr]$/', $str)){
		//Maskulinae/Neutrae Singular auf -el, -er oder -en enden auf -ø, Femininae=>irregulars!
	} elseif(preg_match('/[bcdfghjklmnpqrstvwxyz]$/', $str) and strlen($str)<5){
		//Die meisten Einsilbigen mit Singular auf Konsonant haben Plural auf -e, Rest => irregular
		$str.="e";
	}
	return $str;
}     
?>
