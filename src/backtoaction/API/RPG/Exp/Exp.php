<?php
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 4:48 PM 19/02/2018
 */
namespace backtoaction\API\RPG\Exp;

use backtoaction\API\RPG\Exp\ExpAPI;
use backtoaction\API\Database\DatabaseAPI;

class Exp implements ExpAPI {

    const EXP_API_VERSION = 0.01;

    const ERROR = -1;

    public static $instance;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->db = DatabaseAPI::getInstance();
    }

    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function getPlayerDB(string $user) {
        return $this->db->getPlayerDatabase($user);
    }

    public function getPlayerExp(string $user) {
        $i = $this->getPlayerDB($user);
        $exp = $i->get("exp");
        return $exp;
    }

    public function getPlayerMaxExp(string $user) {
        $i = $this->getPlayerDB($user);
        $maxexp = $i->get("maxexp");
        return $maxexp;
    }

    public function setPlayerExp(string $user, int $exp) {
        $i = $this->getPlayerDB($user);
        if($exp >= 0) {
            $i->set("exp", $exp);
            $i->save();
        }else{
            $this->plugin->getLogger()->notice("Attempt To Set Player's Exp With Under 0 Numerical. Request Rejected.");
        }
    }

    public function setPlayerMaxExp(string $user, int $exp) {
        $i = $this->getPlayerDB($user);
        if($exp >= 0) {
            $i->set("maxexp", $exp);
            $i->save();
        }else{
            $this->plugin->getLogger()->notice("Attempt To Set Player's Max Exp With Under 0 Numerical. Request Rejected.");
        }
    }

    public function addPlayerExp(string $user, int $exp) {
        $i = $this->getPlayerDB($user);
        $maxlevel = $this->level->getMaxLevel();
        $plevel = $this->plugin->getPlayerLevel($user);
        if($plevel < $maxlevel){
            if($exp >= 0) {
                $current = $i->getPlayerExp();
                $maxexp = $i->getPlayerMaxExp();
                $cal = $current + $exp;
                if($cal >= $maxexp) {
                    $n = $cal - $maxexp;
                    $this->level->LevelUp($user); // maybe send a level up smth event
                    $i->set("exp", $n);
                    $i->save();
                    $this->checkExp($user);// todo loop this // gonna put checker if there is more exp.
                }else{
                    $i->set("exp", $cal);
                    $i->save();
                }
            }else{
                $this->plugin->getLogger()->notice("Attempt To Add Player's Exp With Under 0 Numerical. Request Rejected.");
            }
        } // todo return attempt fail
    }

    public function checkExp(string $user) {
        $exp = $this->getPlayerExp($user);
        $maxexp = $this->getPlayerMaxExp($user);
        if($exp >= $maxexp) {
            $this->addPlayerExp($user, 0); // duhh :P
            $this->plugin->getLogger()->notice("Attempt To Check Player's Exp If More Than MaxExp. Request Accepted.");
        }
    }

    public function addPlayerMaxExp(string $user) { // idk bout this... 
        $i = $this->getPlayerDB($user);
        $currentmaxexp = $this->getPlayerMaxExp($user);
        $multiplier = $this->db->getSetting()->get("player_database.exp.maxexp_multiplier");
        $adder = $this->db->getSetting()->get("player_database.exp.add_maxexp_each_lvl_up");
        if($multiplier <= 0) { // excuse me! the heck you set multiplier to 0 or less??!! are fkng dumb??
            $this->plugin->getLogger()->notice("[ExpAPI] Please Set Your Player's Database Setting Properly. Look Here I Found You Set The MaxExp Multiplier To" . $multiplier);
            $this->plugin->getLogger()->notice("Attempt To Use Default Value Of Multiplier To Two. Access Granted.");
            $multiplier_2 = 2;
            $this->db->getSetting()->set("player_database.exp.maxexp_multiplier", $multiplier_2);
            $s = $this->db->getSetting();
            $s->save();
            if($adder <= 0) {
                $this->plugin->getLogger()->notice("[ExpAPI] Please Set Your Player's Database Setting Properly. Look Here I Found You Set The MaxExp Adder To" . $adder);
                $this->plugin->getLogger()->notice("Attempt To Use Default Value Of Adder To Hundred. Access Granted.");
                $adder_2 = 100;
                $s->set("player_database.exp.add_maxexp_each_lvl_up");
                $s->save();

                $calculate_new_maxexp = ($currentmaxexp + $adder_2) * $multiplier_2; // hohoho bad way of mine!!
                $i->set("maxexp", $calculate_new_maxexp);
                $i->save();
            }else{
                $calculate_new_maxexp = ($currentmaxexp + $adder) * $multiplier_2;
                $i->set("maxexp", $calculate_new_maxexp);
                $i->save();
            }
        }elseif($adder <= 0) {
            $this->plugin->getLogger()->notice("[ExpAPI] Please Set Your Player's Database Setting Properly. Look Here I Found You Set The MaxExp Adder To" . $adder);
            $this->plugin->getLogger()->notice("Attempt To Use Default Value Of Adder To A Hundred. Access Granted.");
            $adder_2 = 100;
            $s->set("player_database.exp.add_maxexp_each_lvl_up");
            $s->save();
            $calculate_new_maxexp = ($currentmaxexp + $adder_2) * $multiplier;
            $i->set("maxexp", $calculate_new_maxexp);
            $i->save();
        }else{
            // this is for every setting is OK.
            $cal = ($currentmaxexp + $adder) * $multiplier;
            $i->set("maxexp", $cal);
            $i->save();
        }
        /** player_database.exp.maxexp_multiplier player_database.exp.starter_maxexp player_database.exp.add_maxexp_each_lvl_up
         * use this function when LevelUp or in the add player exp when the exp is more than the maxexp
         * the calculation to add the $exp is depend on the config or the setting.yml 
         * :thonk: hmm maybe a multiplier and plus variable?
         */
    }

    public function reducePlayerExp(string $user) { // heeh... good for thoese wanna do reduce player exp if the player dead lololol.
        $s = $this->db->getSetting();
        $i = $this->getPlayerDB($user);
        $current_exp = $this->getPlayerExp($user);
        $percentage = $s->get("player_database.exp.exp_reduce_percentage");
        if($percentage <= 100) {
            $percentage_new = $percentage / 100;
            if($current_exp >= 1) {
                $cal_reduced = $current_exp * $percentage_new;
                $i->set("exp", $cal_reduced);
                $i->save();
            }else{
                $i->set("exp", 0);
                $i->save();
            }
        }else{
            $this->plugin->getLogger()->notice("[ExpAPI] Please Set Your Player's Database Setting Properly. Look Here I Found You Set The Exp Reduce Percentage To" . $percentage);
            $this->plugin->getLogger()->notice("Attempt To Use Default Value Of Percentage. Access Granted.");
            $s->set("player_database.exp.exp_reduce_percentage", 1);
            $s->save();
            $percentage_new_lol = 1;
            $percentage_new = $percentage_new_lol / 100;
            if($current_exp >= 1) {
                $cal_reduced = $current_exp * $percentage_new;
                $i->set("exp", $cal_reduced);
                $i->save();
            }else{
                $i->set("exp", 0);
                $i->save();
            }
        }
        /**
         * in this function we'll use percentage.
         * why? cause i wanna to.
         * 1 = 100% and 0.5 = 50% got it?
         * the heck why i wanna do dis kind of calculation??!!?
         * heh.
         * how bout i get the percentage and then devide by 100!!
         * exmple the fkng user put 1 percent then 1/100 = 0.01! and then i can do my calculation esiely!! (lol typo :P)
         * the percentage we'll get from setting!! hah!! dannit i so lazy xD
         */
    }

    public function reducePlayerMaxExp(string $user) {
        /**
         * getPlayer lvl
         * do calculation by using Tn = a+r^n-1 :poiit:
         */
    }

}
?>
