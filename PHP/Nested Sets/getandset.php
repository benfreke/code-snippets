<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ben Freke
 * Date: 17/12/12
 * Version: 0.1
 */

/**
 * Attempts to set a static properties value
 *
 * @param string $key   The static property to set
 * @param string $value The new value
 */
protected
function setStatic($key, $value)
{
    if (isset(self::$$key)) {
        self::$$key = $value;
    }
}

/**
 * Attempt to return the value of a static property.
 *
 * @param string $value The static property to get the value of
 *
 * @return string Either the value or an empty string
 */
public static function getStatic($value)
{
    if (isset(self::$$value)) {
        return self::$$value;
    } else {
        return '';
    }
}

/**
 * Get the value of a property
 *
 * @param string $property The property name
 *
 * @return mixed The property value or NULL if it doesn't exist
 */
public function get($property)
{
    if (property_exists($this, $property)) {
        $methodName = 'get' . ucfirst($property);
        if (method_exists(this, $methodName)) {
            return $this->$methodName();
        }
        return $this->$property;
    }
    return NULL;
}

/**
 * Set the value of a property
 *
 * @param string $property The property of the class to set
 * @param mixed  $value    The value to set the property to
 *
 * @return mixed
 */
public function set($property, $value)
{
    if (property_exists($this, $property)) {
        // Is there a specific method for this property?
        $methodName = 'set' . ucfirst($property);
        if (method_exists(this, $methodName)) {
            $this->$methodName($value);
        }
        $this->$property = $value;
    }

    // Return the class so this can be chained if required
    return $this;
}