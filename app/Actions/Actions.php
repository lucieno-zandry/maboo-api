<?php

namespace App\Actions;


abstract class Actions
{
    /**
     * @param string property
     * returns the value of the unique property
     */
    private function getUniqueProperty(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            return false;
        }
    }

    private function getListOfProperties(array $propertyNames): array
    {
        $data = [];

        foreach ($propertyNames as $property) {
            if (property_exists($this, $property)) {
                $data[$property] = $this->$property;
            }
        }

        return $data;
    }

    private function getAllProperties(): array
    {
        $properties = get_object_vars($this);
        $data = [];

        foreach ($properties as $property => $value) {
            $data[$property] = $this->$property;
        }

        return $data;
    }


    /**
     * @param null|string properties
     * returns an array if many properties are mentionned or the value of the unique property
     */
    public function get()
    {
        $args = func_get_args();
        $instanceData = [];

        if (!empty($args) && count($args) === 1) {
            $instanceData = $this->getUniqueProperty($args[0]);
        } else if (!empty($args) && count($args) > 1) {
            $instanceData = $this->getListOfProperties($args);
        } else {
            $instanceData = $this->getAllProperties();
        }

        return $instanceData;
    }
}