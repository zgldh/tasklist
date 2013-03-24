<?php
class DateTimeAppPeer extends AppPeer
{
    /*
     * (non-PHPdoc) @see AppPeer::isAutoActive()
     */
    public function isAutoActive()
    {
        return true;
    }
    
    /*
     * (non-PHPdoc) @see AppPeer::autoActive()
     */
    public function autoActive($user_id)
    {
        $CI = & get_instance ();
        $CI->load->model ( 'App_active_model', 'app_active_model', true );
        
        $actived_peer = AppActivePeer::model ()->create ( $this->app_id, $user_id );
        $actived_peer->save ();
        return $actived_peer;
    }
    /*
     * (non-PHPdoc) @see AppPeer::getActiveForm()
     */
    public function getActiveForm()
    {
        return null;
    }
}
