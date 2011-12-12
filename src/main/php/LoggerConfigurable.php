<?php

/** 
 * A base class from which all classes which have configurable properties are 
 * extended. Provides a generic setter with integrated validation.  
 */
abstract class LoggerConfigurable {
	
	/** Setter function for boolean type. */
	protected function setBoolean($property, $value) {
		try {
			$this->$property = LoggerOptionConverter::toBooleanEx($value);
		} catch (Exception $ex) {
			$value = var_export($value, true);
			$this->warn("Invalid value given for '$property' property: [$value]. Expected a boolean value. Property not changed.");
		}
	}
	
	/** Setter function for integer type. */
	protected function setInteger($property, $value) {
		try {
			$this->$property = LoggerOptionConverter::toIntegerEx($value);
		} catch (Exception $ex) {
			$value = var_export($value, true);
			$this->warn("Invalid value given for '$property' property: [$value]. Expected an integer. Property not changed.");
		}
	}
	
	/** Setter function for LoggerLevel values. */
	protected function setLevel($property, $value) {
		try {
			$this->$property = LoggerOptionConverter::toLevelEx($value);
		} catch (Exception $ex) {
			$value = var_export($value, true);
			$this->warn("Invalid value given for '$property' property: [$value]. Expected a level value. Property not changed.");
		}
	}
	
	/** Setter function for integer type. */
	protected function setPositiveInteger($property, $value) {
		try {
			$this->$property = LoggerOptionConverter::toPositiveIntegerEx($value);
		} catch (Exception $ex) {
			$value = var_export($value, true);
			$this->warn("Invalid value given for '$property' property: [$value]. Expected a positive integer. Property not changed.");
		}
	}
	
	/** Setter for file size. */
	protected function setFileSize($property, $value) {
		try {
			$this->$property = LoggerOptionConverter::toFileSizeEx($value);
		} catch (Exception $ex) {
			$value = var_export($value, true);
			$this->warn("Invalid value given for '$property' property: [$value]. Expected a file size value.  Property not changed.");
		}
	}
	
	/** Setter function for numeric type. */
	protected function setNumeric($property, $value) {
		try {
			$this->$property = LoggerOptionConverter::toNumericEx($value);
		} catch (Exception $ex) {
			$value = var_export($value, true);
			$this->warn("Invalid value given for '$property' property: [$value]. Expected a number. Property not changed.");
		}
	}
	
	/** Setter function for string type. */
	protected function setString($property, $value, $nullable = false) {
		if ($value === null) {
			if($nullable) {
				$this->$property= null;
			} else {
				$this->warn("Null value given for '$property' property. Expected a string. Property not changed.");
			}
		} else {
			try {
				$this->$property = LoggerOptionConverter::toStringEx($value);
			} catch (Exception $ex) {
				$value = var_export($value, true);
				$this->warn("Invalid value given for '$property' property: [$value]. Expected a string. Property not changed.");
			}
		}
	}
	
	/** Triggers a warning. */
	protected function warn($message) {
		$class = get_class($this);
		trigger_error("log4php: $class: $message", E_USER_WARNING);
	}
}



?>