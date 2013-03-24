<?php
class WeatherAppPeer extends AppPeer
{
    /*
     * (non-PHPdoc) @see AppPeer::isAutoActive()
     */
    public function isAutoActive()
    {
        return false;
    }
    /*
     * (non-PHPdoc) @see AppPeer::autoActive()
     */
    public function autoActive($user_id)
    {
        return false;
    }
    /*
     * (non-PHPdoc) @see AppPeer::getActiveForm()
     */
    public function getActiveForm()
    {
        $form = $this->getAppView('WeatherActiveForm.php',null,true);
        return $form;
    }
}
