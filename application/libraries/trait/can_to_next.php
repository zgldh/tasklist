<?php 
trait can_to_next
{
    private $_can_move_to_next = true;
    /**
	*设置能否to next
    */
    public function setCanMoveToNext($if_can)
    {
    	$this->_can_move_to_next = $if_can;
    }
    /**
     * 得到能否to next
     */
	public function canMoveToNext()
	{
		return $this->_can_move_to_next;
	}
}