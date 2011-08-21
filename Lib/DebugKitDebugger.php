<?php
/**
 * DebugKit Debugger class. Extends and enhances core
 * debugger. Adds benchmarking and timing functionality.
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.vendors
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::uses('Debugger', 'Utility');
App::uses('FireCake', 'DebugKit.Lib');
App::uses('DebugTimer', 'DebugKit.Lib');
App::uses('DebugMemory', 'DebugKit.Lib');

/**
 * Debug Kit Temporary Debugger Class
 *
 * Provides the future features that are planned. Yet not implemented in the 1.2 code base
 *
 * This file will not be needed in future version of CakePHP.
 */
class DebugKitDebugger extends Debugger {
/**
 * destruct method
 *
 * Allow timer info to be displayed if the code dies or is being debugged before rendering the view
 * Cheat and use the debug log class for formatting
 *
 * @return void
 */
	public function __destruct() {
		$_this = DebugKitDebugger::getInstance();
		if (Configure::read('debug') < 2 || !$_this->__benchmarks) {
			return;
		}
		$timers = array_values(DebugKitDebugger::getTimers());
		$end = end($timers);
		echo '<table class="cake-sql-log"><tbody>';
		echo '<caption>Debug timer info</caption>';
		echo '<tr><th>Message</th><th>Start Time (ms)</th><th>End Time (ms)</th><th>Duration (ms)</th></tr>';
		$i = 0;
		foreach ($timers as $timer) {
			$indent = 0;
			for ($j = 0; $j < $i; $j++) {
				if (($timers[$j]['end']) > ($timer['start']) && ($timers[$j]['end']) > ($timer['end'])) {
					$indent++;
				}
			}
			$indent = str_repeat(' » ', $indent);

			extract($timer);
			$start = round($start * 1000, 0);
			$end = round($end * 1000, 0);
			$time = round($time * 1000, 0);
			echo "<tr><td>{$indent}$message</td><td>$start</td><td>$end</td><td>$time</td></tr>";
			$i++;
		}
		echo '</tbody></table>';
	}
/**
 * Start an benchmarking timer.
 *
 * @param string $name The name of the timer to start.
 * @param string $message A message for your timer
 * @return bool true
 * @deprecated use DebugTimer::start()
 */
	public static function startTimer($name = null, $message = null) {
		return DebugTimer::start($name, $message);
	}

/**
 * Stop a benchmarking timer.
 *
 * $name should be the same as the $name used in startTimer().
 *
 * @param string $name The name of the timer to end.
 * @return boolean true if timer was ended, false if timer was not started.
 * @deprecated use DebugTimer::stop()
 */
	public static function stopTimer($name = null) {
		return DebugTimer::stop($name);
	}

/**
 * Get all timers that have been started and stopped.
 * Calculates elapsed time for each timer. If clear is true, will delete existing timers
 *
 * @param bool $clear false
 * @return array
 * @deprecated use DebugTimer::getAll()
 */
	public static function getTimers($clear = false) {
		return DebugTimer::getAll($clear);
	}

/**
 * Clear all existing timers
 *
 * @return bool true
 * @deprecated use DebugTimer::clear()
 */
	public static function clearTimers() {
		return DebugTimer::clear();
	}

/**
 * Get the difference in time between the timer start and timer end.
 *
 * @param $name string the name of the timer you want elapsed time for.
 * @param $precision int the number of decimal places to return, defaults to 5.
 * @return float number of seconds elapsed for timer name, 0 on missing key
 * @deprecated use DebugTimer::elapsedTime()
 */
	public static function elapsedTime($name = 'default', $precision = 5) {
		return DebugTimer::elapsedTime($name, $precision);
	}

/**
 * Get the total execution time until this point
 *
 * @return float elapsed time in seconds since script start.
 * @deprecated use DebugTimer::requestTime()
 */
	public static function requestTime() {
		return DebugTimer::requestTime();
	}

/**
 * get the time the current request started.
 *
 * @return float time of request start
 * @deprecated use DebugTimer::requestStartTime()
 */
	public static function requestStartTime() {
		return DebugTimer::requestStartTime();
	}

/**
 * get current memory usage
 *
 * @return integer number of bytes ram currently in use. 0 if memory_get_usage() is not available.
 * @deprecated Use DebugMemory::getCurrent() instead.
 **/
	public static function getMemoryUse() {
		return DebugMemory::getCurrent();
	}

/**
 * Get peak memory use
 *
 * @return integer peak memory use (in bytes).  Returns 0 if memory_get_peak_usage() is not available
 * @deprecated Use DebugMemory::getPeak() instead.
 */
	public static function getPeakMemoryUse() {
		return DebugMemory::getPeak();
	}

/**
 * Stores a memory point in the internal tracker.
 * Takes a optional message name which can be used to identify the memory point.
 * If no message is supplied a debug_backtrace will be done to identifty the memory point.
 * If you don't have memory_get_xx methods this will not work.
 *
 * @param string $message Message to identify this memory point.
 * @return boolean
 * @deprecated Use DebugMemory::getAll() instead.
 */
	public static function setMemoryPoint($message = null) {
		return DebugMemory::record($message);
	}

/**
 * Get all the stored memory points
 *
 * @param boolean $clear Whether you want to clear the memory points as well. Defaults to false.
 * @return array Array of memory marks stored so far.
 * @deprecated Use DebugMemory::getAll() instead.
 */
	public static function getMemoryPoints($clear = false) {
		return DebugMemory::getAll($clear);
	}

/**
 * Clear out any existing memory points
 *
 * @return void
 * @deprecated Use DebugMemory::clear() instead.
 */
	public static function clearMemoryPoints() {
		return DebugMemory::clear();
	}

/**
 * Handles object conversion to debug string.
 *
 * @param string $var Object to convert
 */
	public function outputError($data = array()) {
		extract($data);
		if (is_array($level)) {
			$error = $level['error'];
			$code = $level['code'];
			if (isset($level['helpID'])) {
				$helpID = $level['helpID'];
			} else {
				$helpID = '';
			}
			$description = $level['description'];
			$file = $level['file'];
			$line = $level['line'];
			$context = $level['context'];
			$level = $level['level'];
		}
		$files = $this->trace(array('start' => 2, 'format' => 'points'));
		$listing = $this->excerpt($files[0]['file'], $files[0]['line'] - 1, 1);
		$trace = $this->trace(array('start' => 2, 'depth' => '20'));

		if ($this->_outputFormat == 'fb') {
			$kontext = array();
			foreach ((array)$context as $var => $value) {
				$kontext[] = "\${$var}\t=\t" . $this->exportVar($value, 1);
			}
			$this->_fireError($error, $code, $description, $file, $line, $trace, $kontext);
		} else {
			$data = compact(
				'level', 'error', 'code', 'helpID', 'description', 'file', 'path', 'line', 'context'
			);
			echo parent::output($data);
		}
	}
/**
 * Create a FirePHP error message
 *
 * @param string $error Name of error
 * @param string $code  Code of error
 * @param string $description Description of error
 * @param string $file File error occured in
 * @param string $line Line error occured on
 * @param string $trace Stack trace at time of error
 * @param string $context context of error
 * @return void
 */
	protected function _fireError($error, $code, $description, $file, $line, $trace, $context) {
		$name = $error . ' - ' . $description;
		$message = "$error $code $description on line: $line in file: $file";
		FireCake::group($name);
		FireCake::error($message, $name);
		FireCake::log($context, 'Context');
		FireCake::log($trace, 'Trace');
		FireCake::groupEnd();
	}
}


DebugKitDebugger::getInstance('DebugKitDebugger');