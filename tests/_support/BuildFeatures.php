<?php
class BuildFeatures{
    public function BuildFeatureFiles($runId,$section){
        for ($i=0;$i<count($runId);$i++){
            $title=$runId[$i]["title"];
            $steps=$runId[$i]["custom_steps"];
            $case_id=$runId[$i]["case_id"];
            $run_id=$runId[$i]["run_id"];
            $fileName=$run_id.$case_id;
            $this->createFeatures($fileName,$section,$title,$steps);
        }
        
    }
    public function createFeatures($fileName,$section,$title,$steps){
        fopen($fileName."feature", 'w+');     
        if (strpos($steps, "Examples")!==false){
            $scenario="Scenario Outline: ".$title;
        }
        else{
            $scenario="Scenario: ".$title;
        }        
        $data=[$scenario,$steps];
        file_put_contents($fileName."feature",$data);
    }
    
    public function getSections(){
        
        
    }
}

