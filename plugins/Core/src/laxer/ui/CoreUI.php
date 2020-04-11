<?php 

namespace laxer\ui;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use pocketmine\utils\TextFormat;
use laxer\pshop\Shop;
use laxer\Core;

class CoreUI {
    
    public function __construct(){
        //
    }
    
    public static function FPShopSign(Shop $shop, $item_name){
        $name = $shop->getName();
        $stock = $shop->getCountItem($item_name);
        $price = $shop->getPrice($item_name);
        $data = [
            TextFormat::colorize('&f&l['.strtoupper($name).'&l&f]'),
            TextFormat::colorize('&e&l'.$item_name),
            TextFormat::colorize('&7$' .$price.'/bcs'),
            TextFormat::colorize('&7' .$stock),
        ];
        return $data;
    }
    
    public static  function Notice($text){
        return TextFormat::colorize('&b[• &fINFO&b •]&7 '.$text.'.');
    }
    public static function Success($text){
        return TextFormat::colorize('&a[• &fINFO&a •]&7 '.$text.'.');
    }
    public static function Danger($text){
        return TextFormat::colorize('&c[• &fINFO&c •]&7 '.$text.'.');
    }
    public static function Warning($text){
        return TextFormat::colorize('&e[• &fINFO&e •]&7 '.$text.'.');
    }
    
    public static function fTitle($text){
        return TextFormat::colorize('&l&0'.strtoupper($text));
    }
    
    public static function fBButton($text){
        return TextFormat::colorize('&l&c'.strtoupper($text));
    }
    
    public static function fButton($text){
        return TextFormat::colorize('&8&l'.strtoupper($text));
    }
    
    public static function Simple(?callable $callable){
        return new SimpleForm($callable);
    }
    
    public static function Custom(?callable $callable){
        return new CustomForm($callable);
    }
    
    public static function Modal(?callable $callable){
        return new ModalForm($callable);
    }
    
    public static function alertContent($text){
        return TextFormat::colorize('&c&l'.$text);
    }
    
    public static function getItemImage(int $id, int $damage){
        $dir = '/items/';
        return $dir.$id.'-'.$damage.'.png';
    }
    
}