<?php
class WeixinAppPeer extends AppPeer
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
        // TODO Auto-generated method stub
        $form = $this->getAppView('WeixinActiveForm.php',null,true);
        return $form;
    }
}
