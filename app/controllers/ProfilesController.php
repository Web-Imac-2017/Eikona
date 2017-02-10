<?php

class ProfilesController
{
    /**
     * Create a new profile for the current user
     */
    public function create()
    {

    }

    /**
     * Return the profile sheet to display
     *
     * @param $profileID ID of the profile to display
     */
    public function display($profileID){ }

    /**
     * Return the description of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function name($profileID){ }

    /**
     * Return the description of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function description($profileID){ }

    /**
     * Return the link to the profile picture of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function picture($profileID){ }

    /**
     * Return the number of views of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function views($profileID){ }

    /**
     * Return the number of views of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function isPrivate($profileID){ }

    /**
     * Return the number of views of the specified profile
     *
     * @param $profileID ID of the profile
     * @param $limit number of posts to return
     */
    public function posts($profileID, $limit = 987654321){ }

    /**
     * Update the specified element of the profile
     *
     * @param $field Field to be updated
     * @param $profileID ID of the profile
     */
    public function update($field, $profileID){ }
    /**
     * Increment by one (or more) the view counter of the specified profile
     *
     * @param $porfileID ID of the profile
     * @param $nbr Number of view to add.
     */
    public function addView($profileID, $nbr = 1){ }

    /**
     * Delete the specified profile and all its dependecies
     *
     * @param $profileToDelete
     */
    public function delete($profileID){ }
}
?>
