<?php
/**
 * Copyrights 2018 BackToAction's Team
 * Created By @HyGlobalHD At 5:27 PM 16/02/2018
 */
namespace backtoaction;

class Main extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {

    const API_VERSION = 0.01; // This plugin API_VERSION

    public function onEnable() {
        // code...
        $this->loadAll();
    }

    public function loadAll() {
        // code...
    }

    public function saveAll() {
        // code...
    }

    public function onDisable() {
        // code...
        $this->saveAll();
    }


}
?>