<?
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 9:39 A.M  17/02/2018
 */
namespace backtoaction\API\Database;

use backtoaction\Main;
use pocketmine\utils\Config;

interface DatabaseAPI  {

    protected static $instance;

    public function _construct(Main $plugin);

    protected static function getInstance();

    protected function makeId(string $user);

    protected function getToken();

    protected function getPlayerDatabase(string $user);

    private function databaseArray();

    public function DATABASE_API_Checker();

    protected function removePlayerData(string $user, string $token);

    protected function checkAgn(string $src);

    protected function getSetting();


}
?>