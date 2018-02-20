<?
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 10:26 AM 17/02/2018
 */
namespace backtoaction\API\Currency;

use backtoaction\Main;
use backtoaction\API\Database\DatabaseAPI;

interface CurrencyAPI {

    public static $instance;

    public function __construct(Main $plugin);

    public static function getInstance();

    private function getDB(string $user);

    public function getPlayerCurrency(string $user);

    public function setPlayerCurrency(string $user, int $currency);

    public function addPlayerCurrency(string $user, int $currency);

    public function reducePlayerCurrency(string $user, int $currency);

    public function CURRENCY_API_Checker();

}
?>