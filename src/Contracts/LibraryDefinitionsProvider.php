<?php

namespace Drewlabs\Libman\Contracts;

interface LibraryDefinitionsProvider
{
    /**
     * Add a library definition to the provider
     * 
     * @param array $value 
     * @return void 
     */
    public function addDefinition(array $value);

    /**
     * Return the an iterable of library definitions
     * 
     * @return array|\Traversable 
     */
    public function definitions();

    /**
     * Persist the definition to the disk
     * 
     * @return void 
     */
    public function persist();
}