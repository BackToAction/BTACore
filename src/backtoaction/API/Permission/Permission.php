<?php
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 3:29 P.M  20/02/2018
 */
namespace backtoaction\API\Permission;

use backtoaction\API\Database\DatabaseAPI;
use backtoaction\API\Permission\PermissionAPI;
use backtoaction\Main;

class Permission implements PermissionAPI {

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->db = new DatabaseAPI::getInstance();
    }

    private function getPlayerDB(string $user) {
        $result = $this->db->getPlayerDatabase($user);
        return $result;
    }

    public function getPlayerPerms(string $user) {
        $i = $this->getPlayerDB($user);
        $result = $i->get("perms");
        foreach($result as $results) { // umm da heck?
            return strtolower($results);// return the fkng array? :thonk: din't test it yet but.. meh
        }
    }

    public function addPermsToPlayer(string $user, string $perm, string $token) { // todo, find a better way to add array.
        $check = $this->getPlayerPerms($user);
        if(strtolower($perm) !== $check) { //check if there is already a perms exists
            $token_check = $this->db->getToken()->get("private_token");
            if($token === $token_check) { // check is the token is valid. // cant misspell a fkng thng
                $push = strtolower($perm);
                $i = $this->getPlayerDB($user);
                array_push($i["perms"], $push);
                $i->save();
            }else{
                $this->plugin->getLogger()->notice("Attempt To Use Unknown Token. Request Rejected.");
            }
        }else{
            $this->plugin->getLogger()->notice("Attempt To Add An Existed Perms. Request Rejected.");
        }
    }

    public function removePermsToPlayer(string $user, string $perm, string $token) {
        $check = $this->getPlayerPerms($user);
        if(strtolower($perm) == $check) { // the perms is exist lol
            $token_check = $this->db->getToken()->get("private_token");
            if($token === $token_check) {
                unset($check[array_search(strtolower($perm), $check, $strict = false)]); // thank for the guide @CortexPE
            }else{
                $this->plugin->getLogger()->notice("Attempt To Use Unknown Token. Request Rejected.");
            }
        }else{
            $this->plugin->getLogger()->notice("Attempt To Remove Non-Exists Perms. Request Rejected.");
        }
    }

    protected function getGroupDB() {
        return $this->db->getGroupDB();
    }

    public function getGroup() {
        $db = $this->getGroupDB();
        $group = $db->get("group");
        foreach($group as $groups) {
            return strtolower($groups);
        }
    }

    public function getGroupPerms(string $groups) {
        $group = strtolower($groups);
        $db = $this->getGroupDB();
        $check = $this->getGroup();
        if($group == $check) {
            $perm = $db->get("group")->get("$group")->get("perms");
            foreach($perm as $perms) {
                return strtolower($perms);
            }
        }
    }

    public function createGroup(string $groups, string $token) {
        $group = strtolower($groups);
        $db = $this->getGroupDB();
        $check = $this->db->getToken()->get("private_token");
        if($group !== $this->getGroup()) {
            if($token === $check) {
                $eater = yaml_parse_file($db);
                $eater += ["group" => [$group => ["perms" => []]]]; // trying SoFe :shrud:
                yaml_emit($eater);
                $db->save();
            }else{
                $this->plugin->getLogger()->notice("Attempt To Use Unknown Token. Request Rejected.");
            }
        }else{
            $this->plugin->getLogger()->notice("Attempt To Create New Group That Already Exist. Request Rejected.");
        }
    }

    public function addPermsToGroups(string $group, string $perms, string $token) {
        // :thonk:
    }
    
}
?>