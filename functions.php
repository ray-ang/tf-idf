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

	$tokens = array_unique($tokens); // remove duplicates
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
	$threshold = $threshold / $num_tokens; // normalized threshold

	$tf_idtf = 0;
	$tf_array = array();
	foreach ($term_tokens as $key => $value) {
		$tf_t = 0;
		foreach ($value as $term) {
			if (in_array($term, $input_tokens)) {
				$num_subtopics = 0;
				foreach ($term_tokens as $ky => $val) {
					if (in_array($term, $val)) {
						$num_subtopics += 1;
					}
				}

				$tf_t += 1 / $num_tokens; // Total normalized TF
				$tf_idtf += (1 / $num_tokens) * (log10($total_subtopics / $num_subtopics)); // Total TF-IDtF
			}
		}

		if ($tf_t >= $threshold) { // signal detection
			$tf_array[$key] = $tf_idtf; // recommended topics
		}
	}

	arsort($tf_array); // sort values descending
	return $tf_array;
}