<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );
class Scaffold extends MY_Controller
{
    public function index()
    {
        echo 'hi scaffold!';
    }
    private $_start_time_seconds = 0;
    private function timeStart()
    {
        $this->_start_time_seconds = time ();
    }
    private function timeCurrent()
    {
        $current = time ();
        return $current - $this->_start_time_seconds;
    }
    private function isOvertime($max_seconds)
    {
        if ($this->timeCurrent () > $max_seconds)
        {
            return true;
        }
        return false;
    }

    public function setAppTriggerParameters()
    {
        $this->needLoginOrExit();

        $this->loadAppTriggerModel();
        $this->loadAppTriggerModel();
        $this->loadAppModel();
        
        if($this->isPostRequest())
        {
            $data = $this->inputPost('trigger');
            if($data)
            {
                $trigger_id = (int)$data['trigger_id'];
                $parameters = explode("\n",$data['parameters']);
                foreach($parameters as $key=>$val)
                {
                    $parameters[$key] = trim($val);
                }
                $parameters = json_encode($parameters);
                $trigger = $this->app_trigger_model->getByPK($trigger_id);
                $trigger->parameters = $parameters;
                $trigger->save();
            }
            
            $data = $this->inputPost('command');
            if($data)
            {
                $command_id = (int)$data['command_id'];
                $parameters = explode("\n",$data['parameters']);
                foreach($parameters as $key=>$val)
                {
                    $parameters[$key] = trim($val);
                }
                $parameters = json_encode($parameters);
        
                $command = $this->app_command_model->getByPK($command_id);
                $command->parameters = $parameters;
                $command->save();
            }
        }
    
        $this->load->view('scaffold/app_editor');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */