<?php

namespace App\Services;


use Symfony\Component\Process\Process; 

class MultiProcess
{
   private array $pullProcess;
   private array $errors;
   private $applyedCount;
   private $processAmount;

   const DEFAULT_WORKING_PROCESS_AMOUNT = 8;


    public function __construct(array $scripts, int $processAmount = null)
    {
        $this->applyedCount = 0;
        $this->errors = array();
        $this->processAmount = $processAmount ?? self::DEFAULT_WORKING_PROCESS_AMOUNT;
        foreach ($scripts as $script) {
            $this->initProcess($script);
        }

        while(count($this->pullProcess)) {
            sleep(1);
            $this->checkFinishAndRun();
        }
    }

    private function initProcess($script)
    {
        $process = new Process($script);
        if(empty($this->pullProcess)) {
            $workingProcesses = array();
        } else {
            $workingProcesses = array_filter($this->pullProcess, function($processData){ return $processData['status'] == 'working';});
        }
        if(count($workingProcesses) < $this->processAmount) {
            $process->start();
            $this->pullProcess[microtime(true) . ':' . implode(' ', $script)] = array('status' => 'working', "process" => $process);
        } else {
            $this->pullProcess[microtime(true) . ':' . implode(' ', $script)] = array('status' => 'waiting', "process" => $process);
        }

        return $process;
    }

    private function checkFinishAndRun()
    {
        if(empty($this->pullProcess)) {
            return;
        }
        $workingProcesses = array_filter($this->pullProcess, function($processData){ return $processData['status'] == 'working';});
        $waitingProcesses = array_filter($this->pullProcess, function($processData){ return $processData['status'] == 'waiting';});

        foreach ($workingProcesses as $processName => $processData) {
            $process = $processData['process'];
            if($process instanceof Process && $process->isTerminated()) {
                echo "Finished: ". microtime(true) . ": " . $processName . ' ' . PHP_EOL;
                $processOutput = json_decode($process->getOutput(), true);
                $this->increaseApplyedCount($processOutput['applyedCount']);
                $this->addErrors($processOutput['errors']);
                unset($this->pullProcess[$processName]);
                if(!empty($waitingProcesses)) {
                    $processNameToStart = key($waitingProcesses);
                    array_shift($waitingProcesses);
                    $this->pullProcess[$processNameToStart]['status'] = 'working';
                    $this->pullProcess[$processNameToStart]['process']->start();
                }
            }
        }
    }

    private function increaseApplyedCount(int $maount)
    {
        $this->applyedCount = $this->applyedCount + $maount;
    }

    public function getApplyedCount()   
    {
        return $this->applyedCount;
    }

    private function addErrors($errors)
    {   
        foreach($errors as $error) {
            $this->errors[] = $errors;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
