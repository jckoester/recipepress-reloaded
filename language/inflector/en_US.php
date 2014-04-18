<?php
/*
 * Created on 15.10.2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$uncountables=array (
    'access',
    'advice',
    'art',
    'baggage',
    'dances',
    'equipment',
    'fish',
    'fuel',
    'furniture',
    'food',
    'heat',
    'honey',
    'homework',
    'impatience',
    'information',
    'knowledge',
    'luggage',
    'money',
    'music',
    'news',
    'patience',
    'progress',
    'pollution',
    'research',
    'rice',
    'sand',
    'series',
    'sheep',
    'sms',
    'species',
    'toothpaste',
    'traffic',
    'understanding',
    'water',
    'weather',
    'work',
    'large',
    'medium',
	'small',
);
        
$irregulars=array (
    'child' => 'children',
    'clothes' => 'clothing',
    'man' => 'men',
    'movie' => 'movies',
    'person' => 'people',
    'woman' => 'women',
    'mouse' => 'mice',
    'goose' => 'geese',
    'ox' => 'oxen',
	'leaf' => 'leaves',
	'whole' => 'whole',
);

function regular_plural($str){

	if ( (preg_match('/[sxz]$/', $str) and !preg_match('/es/', $str) ) OR preg_match('/[^aeioudgkprt]h$/', $str)) {
		$str .= 'es';
	} elseif (preg_match('/[^aeiou]y$/', $str) and !preg_match('/ies$/', $str)) {
		//Change "y" to "ies" 
        $str = substr_replace($str, 'ies', -1);
	} elseif ( !preg_match('/es$/', $str) and !preg_match('/ies$/', $str) ) {
		$str .= 's';
	}
	return $str;
}     
?>
