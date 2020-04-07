<?php

function printList($list) {

	echo $list->name;

	if ( $list->mode === 'private' ) {
		echo ' <span style="filter: grayscale(100%); background-color: #000;">&#x1f512;</span>';
	}

	echo '<br>';

	if ( $list->description ) {
		echo nl2br($list->description, false) . '<br>';
	}

}
