<?php

/**
 * Text Preprocessing
 *
 * @param string $text  - text to preprocess
 * 
 * @return array        - processed tokenized terms
 */

function preproc($text) {
	if (! is_string($text)) return '$text parameter should be a string.';

	$drop = ['!', '”', '#', '$', '%', '&', '’', '(', ')', '*', '+', ',', '-', '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|', '}', '~'];
	foreach ($drop as $char) { // drop characters
		$text = str_ireplace($char, '', $text);
		$text = str_ireplace('   ', ' ', $text); // extra spaces
		$text = str_ireplace('  ', ' ', $text); // extra space
	}

	$text = strtolower($text); // lowercase
	$text = trim($text); // trim

	$tokens = explode(' ', $text); // tokenization

	foreach ($tokens as $key => $value) { // stemming
		$arr = ['s' => -1, 'e' => -1, 'ly' => -2, 'ty' => -2, 'ed' => -2, 'er' => -2, 'ous' => -3, 'ful' => -3, 'ion' => -3, 'ing' => -3, 'ment' => -4];
		foreach ($arr as $ky => $val) {
			if ( substr($value, $val) == $ky && strlen(substr($value, 0, $val)) > 2 ) $tokens[$key] = substr($value, 0, $val);
		}
	}

	return $tokens;
}

/**
 * Term Frequency Threshold (TFT)
 *
 * @param string $input      - document or input message
 * @param array $topic_terms - collection of topic terms
 * @param integer $threshold - term frequency threshold (raw)
 * 
 * @return array             - recommended subtopics
 */

function tf_idtf_total($input, $topic_terms, $threshold) {
	if (! is_string($input)) return '$input parameter should be a string.';
	if (! is_array($topic_terms)) return '$topic_terms parameter should be an array.';
	if (! is_integer($threshold) || $threshold < 0) return '$threshold parameter should be a positive integer.';

	$input_tokens = preproc($input); // preprocess input
	ksort($input_tokens);

	$term_tokens = array();
	foreach ($topic_terms as $key => $value) { // preprocess topic terms
		$term_tokens[$key] = preproc($value);
	}
	ksort($term_tokens);

	$num_tokens = count($input_tokens);
	$total_subtopics = count($term_tokens);
	$distinct_tokens = array_unique($input_tokens);
	$num_distinct_tokens = count($distinct_tokens);
	
	$threshold = $threshold / $num_distinct_tokens; // normalized threshold

	$tf_array = array();
	foreach ($term_tokens as $key => $value) {
		$tf_n = 0;
		$tf_idtf = 0;
		foreach ($value as $term) {
			$tf_raw = 0;
			if (in_array($term, $input_tokens)) { // raw TF
				$tf_raw = array_count_values($input_tokens)[$term];
			}

			$num_subtopics = 0;
			foreach ($term_tokens as $ky => $val) {
				if (in_array($term, $val)) {
					$num_subtopics += 1;
				}
			}

			$tf_n += $tf_raw / $num_tokens; // Total normalized TF
			$tf_idtf += ($tf_raw / $num_tokens) * (log10($total_subtopics / $num_subtopics)); // Total TF-IDtF
		}

		$input_count = 0;
		foreach ($distinct_tokens as $input) { // count distinct input in term document
			if (in_array($input, $value)) {
				$input_count += 1;
			}
		}

		if ( ($input_count/$num_distinct_tokens) >= $threshold ) { // signal detection
			$tf_array[$key] = $tf_idtf; // recommended topics
		}
	}

	arsort($tf_array); // sort values descending
	return $tf_array;
}