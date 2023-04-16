<?php
class Product extends ProductCore
{
    public $cstextfield;
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null)
    {
        //Définition des nouveaux champs
        self::$definition['fields']['cstextfield'] = [
            'type' => self::TYPE_HTML,
            'lang' => true,
            'validate' => 'isCleanHtml'
        ];
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
