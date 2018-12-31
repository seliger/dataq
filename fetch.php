<?php
    ob_start();
    header('Content-Type: text/plain; charset=UTF-8');
    

    function _matcher($m, $str) {
        if(preg_match('/.*\/(.*)\.txt/', $str, $matches))
            $m[] = $matches[1];
        return $m;
    }

    $curdir = getcwd();
    $statedir = $curdir.DIRECTORY_SEPARATOR."state";
    $datadir = $curdir.DIRECTORY_SEPARATOR."data";

    $globpattern = $datadir.DIRECTORY_SEPARATOR."*";
    $datafiles = array_diff(glob($globpattern), glob("$globpattern", GLOB_ONLYDIR));

    $datanames = array_reduce($datafiles, '_matcher', array());

    if(array_key_exists("job", $_GET)
        and is_numeric($_GET["job"]) 
        and intval($_GET["job"]) > 0
        and array_key_exists("data", $_GET)
        and in_array($_GET["data"], $datanames)) {

        $jobnumber = str_pad($_GET["job"], 6, "0", STR_PAD_LEFT);
        $dataname = $_GET["data"];

        $datafile = $datadir.DIRECTORY_SEPARATOR.$dataname.".txt";
        $statefile = $statedir.DIRECTORY_SEPARATOR.$dataname."_".$jobnumber.".dat";

        #echo "JOB: ".$jobnumber."  DATA FILE: ".$datafile."  STATE_FILE: ".$statefile."\n";

        $datalines = file($datafile);

        if (count($datalines) > 0) {
            $sfh = @fopen($statefile, "c+");

            if ($sfh) {
                # Wait for and lock the state file.
                flock($sfh, LOCK_EX);
    
                if (filesize($statefile) > 0) {
                    $linenumber = trim(fread($sfh, filesize($statefile)));
                } else {
                    $linenumber = 0;
                }

                if ($linenumber < count($datalines)) {
                    if (strlen(trim($datalines[$linenumber])) > 0) {
                        echo trim($datalines[$linenumber])."\n";
                    } else {
                        # No content...
                        http_response_code(204);
                    }

                    ++$linenumber;

                    ftruncate($sfh, 0);
                    rewind($sfh);
                    fwrite($sfh, $linenumber);
                    fflush($sfh);
                } else {
                    # Using 205 as a signal that there is no more
                    # data to be returned.
                    http_response_code(205);
                }
    
                fclose($sfh);
            } else {
                http_response_code(500);
                echo "Could not open/create state file ".$statefile.".\n";
            }                
        } else {
            http_response_code(500);
            echo "No data was read from ".$datafile."\n";
        }

    } else {
        http_response_code(400);
        echo "Invalid input\n";
    }

    ob_end_flush()
?>