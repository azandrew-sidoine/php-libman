<?php

namespace Drewlabs\Libman\Contracts;

interface InstallableLibraryConfigInterface extends LibraryConfigInterface
{

    /**
     * Returns the library configured package name
     *
     * @return string
     */
    public function getPackage();

    /**
     * Returns the library installer type. The type information is
     * used by the intaller factory to create the library installer instance
     *
     * @return string
     */
    public function getType();


    /**
     * Returns the version of the library to be installed
     * 
     * @return int|null 
     */
    public function getVersion();


    /**
     * Returns repository configuration of the library
     * 
     * @return LibraryRepositoryConfigInterface|LibraryRepositoryConfigInterface[]  
     */
    public function getRepository();


    /**
     * Check if the library is a private library
     * 
     * @return bool 
     */
    public function isPrivate();
}
