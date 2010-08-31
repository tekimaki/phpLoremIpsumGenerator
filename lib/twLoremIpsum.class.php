<?php

/**
 * twLoremIpsum library.
 *
 * @package     twLoremIpsum
 * @subpackage  lib
 * @author      Arkadiusz Tułodziecki
 * @version     SVN: $Id: twLoremIpsum.class.php 21 2010-08-31 11:02:39Z atulodziecki $
 */
class twLoremIpsum {
	static protected $gen_unique_words = array();

	protected $sentences_data = null;
	protected $words_data = null;

	/**
	 * Unique String generator
	 *
	 * @param int $max  Maximum string lengtht
	 * @param int $min  Minimum string lengtht
	 * @param string $namespace  Optional namespace to separate unique generator lists
	 * @return string
	 */
	public function generateUniqueString($max = 0, $min = 0, $namespace = 'default') {
		$length = $this->getRandomLimit($max, $min);
		return twRandGenerator::getUniqueString($length, $namespace);
	}

	/**
	 * Unique Strings generator
	 *
	 * @param int $num  Number of strings to generate
	 * @param int $max  Maximum string lengtht
	 * @param int $min  Minimum string lengtht
	 * @param string $namespace  Optional namespace to separate unique generator lists
	 * @return array
	 */
	public function generateUniqueStrings($num, $max = 0, $min = 0, $namespace = 'default') {
		$out = array();
		$length = $max;

		$change_lenght = false;
		if ($min > $max && $min > 0) {
			$change_lenght = true;
		}

		if ($change_lenght === true) {
			for($i=0;$i<$num;$i++) {
				$out[] = twRandGenerator::getUniqueString(rand($min, $max), $namespace);
			}
		} else {
			for($i=0;$i<$num;$i++) {
				$out[] = twRandGenerator::getUniqueString($length, $namespace);
			}
		}
		return $out;
	}

	/**
	 * Paragraph generator
	 *
	 * @param int $max  Maximum number of sentences in paragraph
	 * @param int $min  Minimum number of sentences in paragraph
	 * @return string
	 */
	public function generateParagraph($max = 10, $min = 0) {
		return $this->generateRandomParagraph($max, $min);
	}

	/**
	 * Paragraphs generator
	 *
	 * @param int $num  Number of paragraps to generate
	 * @param int $max  Maximum number of sentences in paragraph
	 * @param int $min  Minimum number of sentences in paragraph
	 * @return array
	 */
	public function generateParagraphs($num, $max = 10, $min = 0) {
		$out = array();
		for($i=0;$i<$num;$i++) {
			$out[] = $this->generateRandomParagraph($max, $min);
		}
		return $out;
	}

	/**
	 * Unique word generator
	 *
	 * @param int $max  Maximum number of letters in word
	 * @param int $min  Minimum number of letters in word
	 * @param string $namespace  Optional namespace to separate unique generator lists
	 * @return string
	 */
	public function generateUniqueWord($max = 10, $min = 0, $namespace = 'default') {
		return $this->getRandomWord($max, $min, true, $namespace);
	}

	/**
	 * Unique words generator
	 *
	 * @param int $num  Number of words to generate
	 * @param int $max  Maximum number of letters in word
	 * @param int $min  Minimum number of letters in word
	 * @param string $namespace  Optional namespace to separate unique generator lists
	 * @return array
	 */
	public function generateUniqueWords($num, $max = 10, $min = 0, $namespace = 'default') {
		$out = array();
		for($i=0;$i<$num;$i++) {
			$out[] = $this->getRandomWord($max, $min, true, $namespace);
		}
		return $out;
	}

	/**
	 * Word generator
	 *
	 * @param int $max  Maximum number of letters in word
	 * @param int $min  Minimum number of letters in word
	 * @return string
	 */
	public function generateWord($max = 10, $min = 0) {
		return $this->getRandomWord($max, $min);
	}

	/**
	 * Words generator
	 *
	 * @param int $num  Number of words to generate
	 * @param int $max  Maximum number of letters in word
	 * @param int $min  Minimum number of letters in word
	 * @return array
	 */
	public function generateWords($num, $max = 10, $min = 0) {
		$out = array();
		for($i=0;$i<$num;$i++) {
			$out[] = $this->getRandomWord($max, $min);
		}
		return $out;
	}

	/**
	 * Lists generator
	 *
	 * @param int $num  Number of lists to generate
	 * @return string
	 */
	public function generateLists($num) {
		;
	}

	/**
	 * Bytes generator
	 *
	 * @param int $num  Number of bytes to generate
	 * @return string
	 */
	public function generateBytes($num) {
		;
	}

	protected function generateRandomParagraph($max, $min) {
		$limit = $this->getRandomLimit($max, $min);
		$out = '';
		for($i=0;$i<$limit;$i++) {
			$out .= ucfirst($this->generateRandomSentence()).'. ';
		}
		return trim($out);
	}

	protected function generateRandomSentence() {
		$sentences_data = & $this->getSentences();
		$rand_keys = array_rand($sentences_data['sentences_list'], rand(1, 3));
		if (!is_array($rand_keys)) {
			return $sentences_data['sentences_list'][$rand_keys];
		}
		$rand_data = array();
		foreach ($rand_keys as $key) {
			$rand_data[] = $sentences_data['sentences_list'][$key];
		}

		return implode(', ',$rand_data);
	}

	protected function getRandomWord($max, $min, $unique = false, $namespace = null) {
		$words_data = & $this->getWords();
		if ($max == 0) {
			return $this->getRandomWordFromArray($words_data['words_list'], $unique, $namespace);
		}
		if ($min > $max) {
			throw new Exception(sprintf('%s: min value can\'t be bigger then max value', __METHOD__));
		}
		$sum_array = array();
		for($i=$min;$i<=$max;$i++) {
			if (isset($words_data['words_for_len'][$i])) {
				$sum_array = array_merge($sum_array, array_values($words_data['words_for_len'][$i]));
			}
		}
		return $this->getRandomWordFromArray(array_flip($sum_array), $unique, $namespace);;
	}

	protected function getRandomWordFromArray($data, $unique, $namespace) {
		$words_data = & $this->getWords();
		if ($unique !== true) {
			$rand_key = array_rand($data, 1);
			return $words_data['words_list'][$rand_key];
		} else {
			if (!isset(self::$gen_unique_words[$namespace])) {
				self::$gen_unique_words[$namespace] = array();
			}
			do {
				$rand_key = array_rand($data, 1);
				if (count(self::$gen_unique_words[$namespace]) >= count($data)) {
					throw new Exception(sprintf('%s: unique words limit `%d` reached', __METHOD__, count($data)));
				}
			} while (in_array($rand_key, self::$gen_unique_words[$namespace]));
			self::$gen_unique_words[$namespace][] = $rand_key;
			return $words_data['words_list'][$rand_key];
		}
	}

	protected function getRandomLimit($max, $min) {
		if ($min > $max && $min > 0) {
			return rand($min, $max);
		} else {
			return $max;
		}
	}

	protected function getSentences() {
		if (is_null($this->sentences_data)) {
			$this->loadSentences();
		}
		return $this->sentences_data;
	}

	protected function loadSentences() {
		$this->sentences_data = json_decode(file_get_contents(dirname ( __FILE__ ) . '/../data/sentences_data.json'), true);
	}

	protected function getWords() {
		if (is_null($this->words_data)) {
			$this->loadWords();
		}
		return $this->words_data;
	}

	protected function loadWords() {
		$this->words_data = json_decode(file_get_contents(dirname ( __FILE__ ) . '/../data/words_data.json'), true);
	}
}
?>