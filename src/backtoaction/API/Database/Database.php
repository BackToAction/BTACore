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

    private $error = -1;

    static private $instance;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    static private function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function makeId(string $user) {
        $name = strtolower(string $user);
        $token = "asdfghjklqwertyuiopHyGlobalHDzxcvbnmlkjhgfdsa";
        $name_1 = strtok(string $name, string $token);
        $algo = "md5"; $algo_2 = "sha256";
        $id = hash(string $algo, string $name_1) + hash(string $algo_2, string $name);
        return $id;
    }

    private function getPlayerDatabase(string $user) {
        $id = $this->makeId(string $user);
        $result = new \pocketmine\utils\Config($this->plugin->getDataFolder() . "\\db\\" . $id . ".yml", CONFIG::YAML, $this->databaseArray());
        return $result;
    }

    private function databaseArray() {
        $result = array[
            "currency" => 0,
            "exp" => 0, "maxexp" => 25,
            "level" => 1,
            "stats" => [
                "ap" => 5,
                "str" => 1, "dex" => 1, "int" => 1, "luck" => 1,
            ],
            "perms" => [],
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

    private function shopArray(string $shop_name, int $shop_id) {
        $result = array[
            "shop" => [
                "Shop_$shop_id" => [
                    "name" => "$shop_name",
                    "shop_id" => $shop_id,
                    "sell" => [],
                    "buy" => [],
            ],
        ],
    ];
    return $result;
    }

    private function getShopDatabase(string $shop_name, int $shop_id) {
        $ids = $this->makeId(strtolower($shop_name));
        $result = new \pocketmine\utils\Config($this->plugin->getDataFolder() . "\\shop\\" . $ids . ".yml", CONFIG::YAML, $this->shopArray($shop_name, $shop_id));
        return $result;
    }



}
?>