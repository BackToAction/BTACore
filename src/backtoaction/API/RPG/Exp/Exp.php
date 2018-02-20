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

    public function getPlayerDB(string $user) {
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
                    $this->level->LevelUp($user);
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

    public function addPlayerMaxExp(string $user, int $exp) {
        $i = $this->getPlayerDB($user);
    }

}
?>
