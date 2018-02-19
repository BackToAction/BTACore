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

class ShopAPI {

    static public $instance;

    public function __construct(Main $plugin);

    static public function getInstance();

}
?>