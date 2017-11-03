<?php
class Entity extends CI_Model {
    private $task;
    private $priority;
    private $size;
    private $group;
    private $deadline;
    private $status;
    private $flag;

    // If this class has a setProp method, use it, else modify the property directly
    public function __set($key, $value) {
        // if a set* method exists for this key, 
        // use that method to insert this value. 
        // For instance, setName(...) will be invoked by $object->name = ...
        // and setLastName(...) for $object->last_name = 
        $method = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
        if (method_exists($this, $method))
        {
            $this->$method($value);
            return $this;
        }

        // Otherwise, just set the property value directly.
        $this->$key = $value;
        return $this;
    }

    public function __get($key)
    {
        $method = 'get'. str_replace(' ','', ucwords(str_replace(['-', '_'], ' ', $key)));
        if (method_exists($this, $method))
            return $this->method();
        return $this->$key; 
    } 

    public function setTask($task)
    {
        if (!isValidTask($task))
            return false;
        $this->task = $task;
        return true;
    }

    public function setPriority($priority)
    {
        if (!isValidPriority($priority))
            return false;
        $this->priority = $priority;
        return true;
    }

    public function setSize($size)
    {
        if (!isValidSize($size))
            return false;

        $this->size = $size;
        return true;
    }

    public function setGroup($group)
    {
        if (!isValidGroup($group))
            return false;
        $this->group = $group;
        return true;
    }

}

/* property validation functions */
function isValidTask($task)
{
    $pattern = '/^[a-z0-9 \-_]{1,64}$/i';
    return preg_match($pattern, $task);
}

function isValidPriority($priority)
{
    return is_int($priority) && $priority >= 0 && $priority < 4; 
}

function isValidSize($size)
{
    return is_int($size) && $size >= 0 && $size < 4; 
}

function isValidGroup($group)
{
    return is_int($group) && $group >= 0 && $group < 5;
}
