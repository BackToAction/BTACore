<?
/**
 * Copyrights 2018 BackToAction's Team <https://github.com/BackToAction/>
 * Created By @HyGlobalHD On 4:19 PM 17/02/2018
 */

 /**
  * Removed As To Be Moved To Another Plugin That Would Be Depend On This Plugin.
  */
namespace backtoaction\API\Currency\Shop;

use backtoaction\Main;
use backtoaction\API\Database\DatabaseAPI;
use backtoaction\API\Currency\CurrencyAPI;

class Shop implements ShopAPI {

    const SHOP_API_VERSION = 0.01;

    static public $instance;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->db = new DatabaseAPI::getInstance();
        $this->currency = new CurrencyAPI::getInstance();
    }

    static public function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return $instance;
    }
        /**
         *  $name = shop name
         *  $id = shop unique ids
         */
    public function registerShop(string $shop_name, int $shop_id) {
        $this->db->getShopDatabase($shop_name, $shop_id);
    }
        /**
         * $item_name = item for sale
         * $item_id = item's id
         * $item_meta = the items meta
         * $amount = item amounts. if -1 = infinity.
         * $is_inf = check is item that for sale is infinity
         */    
    public function pushItem(string $shop_name, int $shop_id, string $item_name, int $item_id, string $item_meta, int $amount) {
        $sdb = $this->db->getShopDatabase($shop_name, $shop_id);
    }

}
?>