<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Arr;
use LogicException;
use ReflectionClass;
use ReflectionProperty;

class BaseDto
{
    protected bool $autoFillFromRequest = true;
    
    /* ------------------------------------------------------------------------------------------------------------------------------ construct -+- */
    public function __construct()
    {
        if ($this->autoFillFromRequest) {
            $this->fillFromRequest();
        }
    }
    
    /* ---------------------------------------------------------------------------------------------------------------------- fill From Request -+- */
    /**
     * Attempt to fill the DTO from the request object.
     */
    public function fillFromRequest()
    {
        // Get a list of properties of this DTO object.
        $properties = $this->getProperties();
        
        // Get request input.
        $requestData = request()->toArray();
        
        foreach ($properties as $property) {
            if (!Arr::has($requestData, $property)) {
                continue;
            }
            
            // Populate the DTO properties.
            $this->{$property} = Arr::get($requestData, $property);
        }
    }
    
    
    /* ------------------------------------------------------------------------------------------------------------------------- get Properties -+- */
    /**
     * Get a list of DTO object's public properties.
     *
     * @return array
     */
    public function getProperties(): array
    {
        $reflection = new ReflectionClass(get_class($this));
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        
        return array_map(fn($property) => $property->getName(), $properties);
    }
    
    /* ------------------------------------------------------------------------------------------------------------------------------- to Array -+- */
    /**
     * Return data contained in DTO object as associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        // Get a list of properties of this DTO object.
        $properties = $this->getProperties();
        
        $array = [];
        
        foreach ($properties as $property) {
            if (!isset($this->{$property})) {
                continue;
            }
            
            // Populate the DTO properties.
            $array[$property] = $this->{$property};
        }
        
        return $array;
    }
    
    /* ------------------------------------------------------------------------------------------------------------------------------------ set -+- */
    public function __set(string $name, $value): void
    {
        $class = get_class($this);
        // Protect DTO objects from undeclared property assignment.
        throw new LogicException("Cannot set undeclared properties on DTOs. Tried to set nonexistent property '$name' on '$class'.");
    }
}
