<?php

class ProfilesController
{
    /**
     * Create a new profile for the current user
     */
    public function create(){ }

    /**
     * Return the profile sheet to display
     *
     * @param $profileID ID of the profile to display
     */
    public function display($profileID){ }

    /**
     * Return the specified element about the profile
     *
     * @param $profileID ID of the profile
     * @param $whatToGet field to retrieve
     */
    public function get($profileID, $whatToGet){ }

    /**
     * Update the specified element of the profile
     *
     * @param $profileID ID of the profile
     * @param $whatToSet Field to be updated
     */
    public function update($profileID, $whatToSet){ }

    /**
     * Delete the specified profile and all its dependecies
     *
     * @param $profileToDelete
     */
    public function delete($profileID){ }
}
?>
