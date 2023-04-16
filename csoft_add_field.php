<?php

class Csoft_add_field extends Module
{
    public function __construct()
    {
        $this->name                   = 'csoft_add_field';
        $this->version                = '1.0.0';
        $this->author                 = 'ComonSoft';
        $this->bootstrap              = true;
        $this->ps_versions_compliancy = array(
            'min' => '1.7.8',
        );
        parent::__construct();
        $this->displayName      = $this->trans('Add wysiwyg field to product page', [], 'Modules.Csoftaddfield.Info');
        $this->description      = $this->trans('Add a wysiwyg text field in product back-office and display  it in the product page', [], 'Modules.Csoftaddfield.Info');
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Csoftaddfield.Info');
    }

    /**
     * Module install
     */
    public function install()
    {
        return parent::install()
            && $this->_installSql()
            && $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
            && $this->registerHook('displayProductExtraContent')
            && $this->registerHook('actionProductSave');
    }

    /**
     * Module uninstall
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * For using new translation system
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * SQL module Modifications
     * @return boolean
     */
    protected function _installSql()
    {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product_lang ADD cstextfield TEXT NULL";
        $returnSql = Db::getInstance()->execute($sqlInstall);
        return $returnSql;
    }

    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    protected function _unInstallSql()
    {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product_lang DROP cstextfield";
        $returnSql = Db::getInstance()->execute($sqlInstall);
        return $returnSql;
    }

    /**
     * Hook for display in product page (back office)
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $product = new Product($params['id_product']);
        $languages = Language::getLanguages(true, (int)Context::getContext()->shop->id);
        $this->context->smarty->assign(
            array(
                'cstextfield' => $product->cstextfield,
                'languages' => $languages,
                'default_language' => $this->context->employee->id_lang,
            )
        );
        return $this->display(__FILE__, 'views/templates/hook/add_field.tpl');
    }

    /**
     * Hook for save in DB after submit in product page (back office)
     */
    public function hookActionProductSave($params)
    {
        $multistoreFeature = $this->get('prestashop.adapter.feature.multistore');
        $shopContext = $this->get('prestashop.adapter.shop.context');
        $customFieldValue = $params['product']->cstextfield;

        if ($isMultistoreEnabled = $multistoreFeature->isActive()) {
            if ($isMultistoreUsed = $multistoreFeature->isUsed()) {
                $shopList = $shopContext->getShops(false, true);
                foreach ($shopList as $shop) {
                    $languages = Language::getLanguages(true, $shop, false);
                    foreach ($languages as $language) {
                        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('UPDATE ' . _DB_PREFIX_ . 'product_lang SET cstextfield = \''
                            . pSQL($customFieldValue[$language['id_lang']]) . "'"
                            . ' WHERE id_shop =' . (int)$shop . ' AND id_lang =' . $language['id_lang'] . ' AND id_product =' . $params['id_product']);
                    }
                }
            }
        }
    }

    /**
     * Hook for display in product page (front office)
     */
    public function hookDisplayProductExtraContent($params)
    {
        $return = [];
        $return[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
            ->setTitle('my field')
            ->setContent($params['product']->cstextfield);
        return $return;
    }
}
