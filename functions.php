<?php

/**
 * Term Frequency–Inverse Document Frequency (TF-IDF)
 *
 * @param string $input      - document or input message
 * @param array $doc         - term document
 * 
 * @return array             - concepts by relevance
 */

function tf_idf_total($input, $doc) {
	if (! is_string($input)) return '$input parameter should be a string.';
	if (! is_array($doc)) return '$doc parameter should be an array.';

	$input = trim(strtolower($input));
	$doc1 = array();
	
	foreach ($doc as $key => $val) {
		$count = 0;
		$tf_idf = 0;

		foreach ($val as $row) {
			$row = strtolower($row);
			$freq = substr_count($input, $row); // raw frequency
			$count = $freq / count( explode(' ', $input) ); // normalized frequency

			$in_doc = 0;
			foreach ($doc as $ky => $vl) {
				if (in_array($row, $vl)) {
					$in_doc += 1;
				}
			}

			$tf_idf += $count * (log10(count($doc) / $in_doc)); // sum TF-IDF
		}
		
		$doc1[$key] = $tf_idf;
	}

	unset($doc);
	gc_collect_cycles();

	arsort($doc1);
	return $doc1;
}
