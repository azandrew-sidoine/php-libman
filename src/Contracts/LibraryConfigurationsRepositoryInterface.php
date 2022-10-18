<?php

namespace Drewlabs\Libman\Contracts;

interface LibraryConfigurationsRepositoryInterface
{

    /**
     * Select a library configuration using developer provided id
     * 
     * @param string|int $id 
     * @return LibraryConfigInterface|InstallableLibraryConfigInterface 
     */
    public function select($id);

    /**
     * Add a new library to the repostory
     * 
     * @param LibraryConfigInterface $libraryConfig 
     * @return void 
     */
    public function add(LibraryConfigInterface $libraryConfig);

    /**
     * Returns the list of available library configuration
     * 
     * @return Generator<int, LibraryConfig, mixed, void> 
     */
    public function selectAll();

}