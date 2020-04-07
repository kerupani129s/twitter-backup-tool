<?php

function printMoment($moment) {

	if ( property_exists($moment, 'time_string') ) {
		echo $moment->time_string . '<br>';
	}

	echo $moment->title;

	echo '<br>';

	if ( property_exists($moment, 'description') ) {
		echo nl2br($moment->description, false) . '<br>';
	}

}
