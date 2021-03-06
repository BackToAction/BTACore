<?
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 9:39 A.M  17/02/2018
 */
namespace backtoaction\API\Database;

use backtoaction\Main;
use pocketmine\utils\Config;

class Database implements DatabaseAPI  {

    const DATABASE_API_VERSION = 0.01;

    const PLAYER_DATA = "player_data";

    const VALID = 1002;

    const INVALID = 1006;

    private $error = -1;

    protected static $instance;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    protected static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function makeId(string $user) {
        $name = strtolower(string $user);
        $token = "asdfghjklqwertyuiopHyGlobalHDzxcvbnmlkjhgfdsa";
        $name_1 = strtok(string $name, string $token);
        $algo = "md5"; $algo_2 = "sha256";
        $tkn = $this->getToken();
        $private_token = hash($algo, string $tkn->get("private_token"));
        $id = hash(string $algo, string $name_1) + hash(string $algo_2, string $name);
        $ids = hash(string $algo_2, string strtolower($private_token) + $id);
        return $ids;
    }

    protected function getToken() { // secure database ? well if the user dont make their token public then its fine.
        $result = array[
            "private_token" => "ChangeThisTokenForOnceOnlyOrElseThePlayerDatabaseWillBeAffected"
        ];// evil way to make user cry LOLOLOLOL
        $tokenDB = new Config($this->plugin->getDataFolder() . "token.yml", CONFIG::YAML, $result);
        return $tokenDB;
    }

    protected function checkToken(string $tokens) { // now use this function :octocat: :change will occure!!:
        $token = $this->getToken()->get("private_token");
        if($tokens === $token) {
            return self::VALID;
        }else{
            return self::INVALID;
        }
    }

    protected function getPlayerDatabase(string $user) {
        $id = $this->makeId(string $user);
        $result = new Config($this->plugin->getDataFolder() . "\\db\\" . $id . ".yml", CONFIG::YAML, $this->databaseArray());
        return $result;
    }

    private function databaseArray() { // please make a pull request add a new string
        $result = array[
            "currency" => 0,
            "exp" => 0, "maxexp" => 25,
            "level" => 1,
            "stats" => [
                "ap" => 5,
                "str" => 1, "dex" => 1, "int" => 1, "luck" => 1,
            ],
            "perms" => [
                "bta.perms.simple",
                "bta.perms.simpler",
            ],
            "permsgroup" => [
                "bta.newbie",
                "bta.newgroup",
            ],
    ];
        return $result;
    }

    public function DATABASE_API_Checker() {
        if(self::DATABASE_API_VERSION < 1){
            $this->plugin->getLogger()->notice("DatabaseAPI still in beta.");
        }else{
            $this->plugin->getLogger()->notice("DatabaseAPI Checked.");
        }
    }

    private function shopArray(string $shop_name, int $shop_id) { // will be update :P fk this
        $result = array[
            "shop" => [],
                "$shop_id" => [
                    "name" => "$shop_name",
                    "shop_id" => $shop_id,
                    "sell" => [],
                    "buy" => [],
            ],
        ],
    ];
    /**
     * make shop = array[shop list]
     * then let the auto pusher to make shop
     * push shop[0] => array["name", "shop_id", etc]
     */
    return $result;
    }

    private function getShopDatabase(string $shop_name, int $shop_id) {
        $ids = $this->makeId(strtolower($shop_name));
        $result = new Config($this->plugin->getDataFolder() . "\\shop\\" . $ids . ".yml", CONFIG::YAML, $this->shopArray($shop_name, $shop_id));
        return $result;
    }

    protected function removePlayerData(string $user, string $token) {
        // should i implement this? hmm :thonk:
        // use fkng unlink()
        $checker = $this->checkToken($token);
        if($checker == self::VALID) {
            $dub = $this->makeId($user);
            $src = $this->plugin->getDataFolder() . "\\db\\" . $dub . ".yml";
            if(file_exists($src)) {
                unlink($src);
                $this->checkAgn($src); //check agn
            }else{
                $this->plugin->getLogger()->warning("[DatabaseAPI] Error In Removing Player Data. Reason: Data Is Not Exists.");
            }
        }else{
            $this->plugin->getLogger()->notice("Attempt To Use A Different Token. Request Rejected.");
        }
    }

    protected function checkAgn(string $src) {// why i add this? well its convience for meh :)
        if(!file_exist($src)) {
            $this->plugin->getLogger()->notice("[Checker] The File Is Not Exists Anymore.");
        }else{
            $this->plugin->getLogger()->notice("[Checker] The File: " . "$src" . " Is Still Exist.");
        } 
    }

    protected function backupPlayerDataAll(string $token) { // this is a hacky way to do. do not use this code if you want to make a backup function
        // function ? to backup the data btch
        $bcksrc = $this->plugin->getDataFolder() . "\\.backup\\";
        // later todo.
    }

    protected function getSetting() { // setting :) .
        $result = new Config($this->plugin->getDataFolder() . "setting.yml" , CONFIG::YAML, array[
            "player_database" => [
                "starter_stats" => [
                    "str" => 5, "int" => 5, "dex" => 5, "luck" => 5,
                ],
                "exp" => [
                    "starter_maxexp" => 10,
                    "maxexp_multiplier" => 2,
                    "add_maxexp_each_lvl_up" => 100,
                    "exp_reduce_percentage" => 1,
                ],
            ]
        ]);
        return $result;
    }
    /** 
     * What happen if  make a config that get the number and allow the variable?
     * ex: variable: 6
     * then get the variable, return get 6
     * then user allowed to make 6 variable with the same sub variable!
     * how to do it:
     * $i = get->("variable"); // 6
     * $var = 1
     * for($var = $i; $var > $i; $var++){ 1 ... 6
     * //codee
     * }
     */

    protected function getGroupDB() {
        $result = new Config($this->getDataFolder() . "\\db\\" . "Group.yml", CONFIG::YAML, array[
            "group" => [
                "example" => [
                    "perms" => [],
                ],
                "bta.newbie" => [
                    "perms" => [],
                ],
                "bta.newgroup" => [
                    "perms" => [],
                ],
            ]
        ]);
        return $result;
    }



}
?>