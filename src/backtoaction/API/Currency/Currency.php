<?
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 10:26 AM 17/02/2018
 */
namespace backtoaction\API\Currency;

use backtoaction\Main;
use backtoaction\API\Database\DatabaseAPI;

class CurrencyAPI implements DatabaseAPI {

    const CURRENCY_API_VERSION = 0.01;
    const ATTEMPT_FAIL = -1;

    static public $instance;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->db = new DatabaseAPI::getInstance();
    }

    static public function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return $instance;
    }

    private function getDB(string $user) {
        return $this->db->getPlayerDatabase($user);
    }

    public function getPlayerCurrency(string $user) {
        $i = $this->getDB($user);
        return $i->get("currency"); // return player currency
    }

    public function setPlayerCurrency(string $user, int $currency) {
        $i = $this->getDB($user);
        if($currency >= 0) {
            $i->set("currency", $currency);
            $i->save();
        }else{
            $this->plugin->getLogger()->notice("Attempt To Set Currency Lower Than 0. Rejected Request.");
        }
    }

    public function addPlayerCurrency(string $user, int $currency) {
        $i = $this->getDB($user);
        if($currency >= 0) {
            $current = $this->getPlayerCurrency($user);
            $i->set("currency", $current + $currency);
            $i->save();
        }else{
            $this->plugin->getLogger()->notice("Attempt To Add Currency Lower Than 0. Rejected Requeset.");
        }
    }

    public function reducePlayerCurrency(string $user, int $currency) {
        $i = $this->getDB($user);
        if($currency >= 0) {
            $current = $this->getPlayerCurrency($user);
            $check = $current - $currency;
            if($check >= 0) {
                $i->set("currency", $check);
                $i->save();
            }else{
                $this->plugin->getLogger()->notice("Attempt To Reduce Player's Currency That Are Less Than The Reduce Currency. Rejected Request.");
                return self::ATTEMPT_FAIL; // note to us: this return is for when making shop, and the reduce currency is more than the currency available.
            }
        }else{
            $this->plugin->getLogger()->notice("Attempt To Use Reduce Function, The Reduce Must Be A Numerical Form And No Need [-]. Rejected Request.");
        }
    }
    
    public function CURRENCY_API_Checker() {
        if(self::CURRENCY_API_VERSION < 1) {
            $this->plugin->getLogger()->notice("CurrencyAPI still in beta.");
        }else{
            $this->plugin->getLogger()->notice("CurrencyAPI Checked.")
        }
    }

}
?>