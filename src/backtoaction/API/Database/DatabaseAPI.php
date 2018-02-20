<?
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 9:39 A.M  17/02/2018
 */
namespace backtoaction\API\Database;

use backtoaction\Main;
use pocketmine\utils\Config;

interface DatabaseAPI  {

    private static $instance;

    public function _construct(Main $plugin);

    private static function getInstance();

    private function makeId(string $user);

    private function getPlayerDatabase(string $user);

    private function databaseArray();

    public function DATABASE_API_Checker();



}
?>