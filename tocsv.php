<?php
/////////////////////////////////////////////////////////////////////////////////////////
//this file does not create csv files, like the filename, it actually creates json files.
/////////////////////////////////////////////////////////////////////////////////////////
$dir = getcwd();//selects the directory of this file at runtime

//for new files
// pic the directory structure below this file or at this file, where the json files are located.
$dir = $dir."/"."28-11-2018/all";
$files = scandir($dir);
// print_r($files);

function firstLevel(){
    global $dir;
    global $files;
    foreach($files as $file){
        if($file !== "." && $file !== ".." && strpos($file,".json")){
            translateFile($file,$dir);
        }
    }
}

function secondLevel(){
    global $dir, $files;
    foreach($files as $filename){
        if($filename !== "." && $filename !== ".." && is_dir($dir."/".substr($filename,0,-5))){
            $folder = substr($filename,0,-5);
            $path = $dir."/".$folder;
            $subfiles = scandir($path);
            foreach($subfiles as $fname){
                if($fname !== "." && $fname !== ".." && !is_dir($path."/".$fname)){
                    // if(is_file($path."/".$fname)){
                        $contents = file_get_contents($path."/".$fname);
                        if(!is_dir($path."/_".$fname)) mkdir($path."/_".$fname);
                        $obj = json_decode($contents);
                        foreach($obj as $k => $prop){
                            file_put_contents($path."/_".$fname."/".$k,json_encode($prop));
                        }
                    // }

                }
            }
        }
    }
}
    function translateFile($file,$dir){
        $contents = file_get_contents($dir."/".$file);
        $dirname = substr($file,0,strpos($file,"."));
        // die($dirname);
        if(!is_dir($dir."/".$dirname)) mkdir($dir."/".$dirname);
        $object = json_decode($contents);
        foreach($object as $name => $property){
            file_put_contents($dir."/".$dirname."/".$name, json_encode($property));
        }
    }
// secondLevel();


function heartRate2timestamp(){
    // $out = [];
    $i = 0;
    $obj = [];
    $find_hr = "hr = ";
    $find_time = "timestamp = ";
    $hr_file = "heartRateLogs";
    global $dir;
    global $files;
    foreach($files as $file){
        if($file !== "." && $file !== ".." && strpos($file,".json")){
            $folder = substr($file,0,strpos($file,"."));
            if(is_file($dir."/".$folder."/".$hr_file)){
                $c = file_get_contents($dir."/".$folder."/".$hr_file);
                $a = explode(";",$c);
                // die("this is the count:".count($a));
                array_pop($a);
                array_shift($a);
                foreach($a as $line){
                        $data = splitLine($line);
                        if($data){
                            $var1 = explode(": BLECommunicationService:",$line);
                            $data["date"] = trim($var1[0]);
                            $obj[$folder][] = $data;
                        }
                }
                file_put_contents($dir."/".$folder."/new_".$folder.".json",json_encode($obj[$folder]));
            }
        }
        file_put_contents($dir."/allObjects.json",json_encode($obj));
    }
}

function splitLine($line){
    $data = [];
    $a = "Logging ibi";//key is ibi
    $b = "ibi size";//key is size
    $c = "hr";
    $d = "timestamp";
    $testArray = ["ibi","size","hr","timestamp"];
    if(strpos($line,$a)&&strpos($line,$b)&&strpos($line,$c)&&strpos($line,$d)){
        $x = explode("=",$line);
                                                                                    // print_r($x);
                                                                                    // exit();
        foreach($x as $k => $v){
            // if(!is_int($k/2)){
                $k2 = $k + 1;
                $var1 = explode(" ",trim($v));
                $key1 = array_pop($var1);
                if(in_array($key1,$testArray)){
                    $var2 = explode(" ",trim($x[$k2]));
                    $data[$key1] = array_shift($var2);
                }
                $var3 = explode(" ",trim($x[$k2]));
                $key2 = array_pop($var3);
                if($data[$key1] === $key2) break;
            // }
        }
        return $data;
    }
    return false;
}
// C:\xampp\htdocs\_ITAY_GROUP\408792\heartRateLogs


// firstLevel();//uncomment and run this file first
// secondLevel();//then comment out the previous line and uncomment this line;
// heartRate2timestamp();//then comment out the two previous lines and uncomment this line;
