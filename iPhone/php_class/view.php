<?php
// Authr: Robert Nowiński
// Email: nowinski.robert@gmail com
// Project: "PSYCHOTESTS" / AGH (PUM)/ Kraków 2019 

require_once ('db_connect.php');
require_once ('settings.php');

class View extends Settings{
   private $databaseConnect;

    function __construct () {
        $this -> databaseConnect = new DatabaseConnect ();
    }

// --------------------------------------------------------------------------------------------- //
// ======================================= TEST IQ SECTION SHOW  =============================== //
// --------------------------------------------------------------------------------------------- //
    public function showTestHTML ($limitTest) {
        if ($this -> databaseConnect -> checkConnect ()[0] == 1) {
            $request = $this -> databaseConnect -> returnTests ($limitTest);
            $tiitle = $request[0];
            $question = $request[1];
            $i = 0;
            foreach ($tiitle as $t) {
                echo '<h2>'.$t.'</h2>'."\n";
                echo '<ul>'."\n";
                foreach ($question[$i] as $q) {
                    $nameandvalue = explode("|", $q);
                    echo '<li><input type = "radio" name="iphone-'.$i.'" value="'.$nameandvalue[1].'">'.$nameandvalue[0].'</input></li>'."\n";
                }
                echo '</ul>'."\n";
                $i++;
            }
        } else {
            echo $this -> databaseConnect -> checkConnect ()[1];
        }

    }

    public function showTestJSON ($limitTest) {
        if ($this -> databaseConnect -> checkConnect ()[0] == 1) {
            $questionsTests = array ();
            $request = $this -> databaseConnect -> returnTests ($limitTest);
            $tiitle = $request[0];
            $question = $request[1];
            $i = 0;
            foreach ($tiitle as $t) {
                $questionsTests[] = array (
                    'question' => trim($t)
                );
                foreach ($question[$i] as $q) {
                    $nameandvalue = explode("|", $q);
                    $questionsTests[] = array (
                        'answer' => trim($nameandvalue[0]), 
                        'value' => trim($nameandvalue[1])
                    );
                }
                $i++;
            }
        } else {
            echo $this -> databaseConnect -> checkConnect ()[1];
        }
        echo json_encode($questionsTests);
    
    }
// ---------------------------------------- END TEST IQ SECTION SHOW --------------------------- //
// ********************************************************************************************* //



// --------------------------------------------------------------------------------------------- //
// ==================================== TEST ISHIHARA SECTION SHOW ============================= //
// --------------------------------------------------------------------------------------------- //

// M: ================================== SHOW TEST AS HTML VERSION ============================= //
    public function ShowImageIshiharaTestHTML ($pool_of_tests) {
        $array = $this -> SetImageIshiharaTest ($pool_of_tests);

        if (count($array) > 0) {
            foreach ($array as $img) {
                echo '<img src = "'.$img.'" width = "500"/>';
            }
        }
    }
// --------------------------------------------------------------------------------------------- //


// M: ================================== SHOW TEST AS JSON VERSION ============================= //
public function ShowImageIshiharaTestJSON ($pool_of_tests) {
    $array = $this -> SetImageIshiharaTest ($pool_of_tests);
    $array_img_ishihara_palet = array ();
    if (count($array) > 0) {
        foreach ($array as $img) {
            $number = explode ('.', $img);
            $number  = explode ('_', $number[0]);

            $array_img_ishihara_palet[] 
            = array ("img" => self::PATH_ISHIHARA_GALLERY.'/'.$img,
                        "value" => $number[count($number) - 1],
                        'image_base64' => base64_encode(file_get_contents(self::PATH_ISHIHARA_GALLERY.$img))
            );
        }
        echo json_encode($array_img_ishihara_palet);
    }
}
// --------------------------------------------------------------------------------------------- //


    private function SetImageIshiharaTest ($pool_of_tests) {
        if (file_exists(self::FOLDER_ISHIHARA_TEST_PALETE_IMG)) {
            $img = scandir (self::FOLDER_ISHIHARA_TEST_PALETE_IMG);
            $array = array ();
            foreach ($img as $path) {
                if ($path != '.' && $path != '..') {
                    array_push ($array, self::FOLDER_ISHIHARA_TEST_PALETE_IMG.'/'.$path);
                }
            }
            $rand_array = array ();
            $rand_limit = count ($array);

            if ($pool_of_tests > $rand_limit) {$pool_of_tests = $rand_limit;}

            $rand = rand (0, $rand_limit -1);
            array_push ($rand_array, $array[$rand]);

            if ($pool_of_tests > 1) {
                for ($i = 1; $i < $pool_of_tests; $i++) {
                    do {
                        $rand = rand (0, $rand_limit -1);
                    } while (in_array($array[$rand], $rand_array));
                    array_push ($rand_array, $array[$rand]);
                }

            }

            return $rand_array;

        } else {  echo "Check the catalog path";}
    }
// ------------------------------------- END ISHIHARA SECTION SHOW ----------------------------- //
// ********************************************************************************************* //


// --------------------------------------------------------------------------------------------- //
// ============================================ USERS AND RESULTS ============================== //
// --------------------------------------------------------------------------------------------- //
    public function AddResult ($id_user, $id_tests, $points) {
        return $this -> databaseConnect -> addData ($id_user, $id_tests, $points);
    }

    public function DeleteTest ($id_user, $id_data_test) {
       return $this -> databaseConnect -> deleteTest($id_user, $id_data_test);
    }

    public function ShowMyTests ($id_user) {
        $show_my_tests =  $this -> databaseConnect -> showMyTests($id_user);
       $array = array ();
        foreach ($show_my_tests as $smt) {
            $array[] = array(
                'id_user' => $smt['id_user'],
                'id_tests' => $smt['id_tests'],
                'points' => $smt['points'],
                'datetime' => $smt['datetime']
            );
        }  
        return $array;
    }

// ------------------------------------- END USERS AND RESULTS -- ----------------------------- //
// ********************************************************************************************* //
}


?>