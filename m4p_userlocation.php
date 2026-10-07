<?php

/**
 * m4p_userlocation
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class m4p_userlocation extends Module
{
    const COOKIE_KEY = 'm4p_userlocation_country';

    const CONFIG_KEYS = [
        'M4P_USERLOCATION_ACTIVE',
        'M4P_USERLOCATION_POPUP',
        'M4P_USERLOCATION_PLACEMENT',
    ];

    const DEFAULTS = [
        'M4P_USERLOCATION_ACTIVE' => 1,
        'M4P_USERLOCATION_POPUP' => 1,
        'M4P_USERLOCATION_PLACEMENT' => self::PLACEMENT_NAV,
    ];

    const PLACEMENT_NAV = 'nav';
    const PLACEMENT_BANNER = 'banner';

    public function __construct()
    {
        $this->name = 'm4p_userlocation';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Modules4Presta';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.6.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Country selector', [], 'Modules.M4puserlocation.Admin');
        $this->description = $this->trans('Lets visitors pick their country, so prices are shown with that country\'s tax before they have an address.', [], 'Modules.M4puserlocation.Admin');
        $this->confirmUninstall = $this->trans('Remove the module and its settings? Customer addresses are not touched.', [], 'Modules.M4puserlocation.Admin');
    }

    public function install()
    {
        foreach (self::DEFAULTS as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        return parent::install()
            && $this->registerHook('actionFrontControllerInitBefore')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayNav2')
            && $this->registerHook('displayBanner');
    }

    public function uninstall()
    {
        foreach (self::CONFIG_KEYS as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue('M4P_USERLOCATION_ACTIVE', (int) Tools::getValue('M4P_USERLOCATION_ACTIVE', 0));
            Configuration::updateValue('M4P_USERLOCATION_POPUP', (int) Tools::getValue('M4P_USERLOCATION_POPUP', 0));

            $placement = (string) Tools::getValue('M4P_USERLOCATION_PLACEMENT', self::PLACEMENT_NAV);
            Configuration::updateValue(
                'M4P_USERLOCATION_PLACEMENT',
                $placement === self::PLACEMENT_BANNER ? self::PLACEMENT_BANNER : self::PLACEMENT_NAV
            );

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&conf=6');
        }

        return $output . $this->countriesNotice() . $this->displayForm();
    }

    /**
     * Says which countries the selector will list and warns when there is only one,
     * which is the usual reason a merchant sees nothing on the front office.
     */
    protected function countriesNotice()
    {
        $countries = $this->getCountries();
        $names = [];
        foreach ($countries as $country) {
            $names[] = $country['name'];
        }

        if (count($countries) < 2) {
            return $this->displayWarning(
                $this->trans('The selector lists the countries enabled under International > Locations > Countries, and only one is enabled right now, so visitors have nothing to choose from.', [], 'Modules.M4puserlocation.Admin')
            );
        }

        $lines = [
            $this->trans('Visitors will be able to pick from: %countries%.', ['%countries%' => implode(', ', $names)], 'Modules.M4puserlocation.Admin'),
            $this->trans('The choice sets the country PrestaShop uses to work out tax, so prices follow your tax rules without this module calculating anything itself.', [], 'Modules.M4puserlocation.Admin'),
            $this->trans('Once a customer has an address on their cart, that address wins and the selector no longer affects prices.', [], 'Modules.M4puserlocation.Admin'),
        ];

        $output = $this->displayInformation(implode('<br>', $lines));

        $netGroups = $this->groupsShowingNetPrices();
        if ($netGroups) {
            $output .= $this->displayWarning(
                $this->trans('These customer groups are set to show prices without tax: %groups%. Their members will not see prices change when they pick a country, because there is no tax in the figure to change.', ['%groups%' => implode(', ', $netGroups)], 'Modules.M4puserlocation.Admin')
            );
        }

        return $output;
    }

    /** @return string[] names of groups displaying prices without tax */
    protected function groupsShowingNetPrices()
    {
        $names = [];

        foreach (Group::getGroups((int) $this->context->language->id) as $group) {
            if ((int) $group['price_display_method'] === Group::PRICE_DISPLAY_METHOD_TAX_EXCL) {
                $names[] = $group['name'];
            }
        }

        return $names;
    }

    public function displayForm()
    {
        $switchValues = [
            ['id' => 'on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.M4puserlocation.Admin')],
            ['id' => 'off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.M4puserlocation.Admin')],
        ];

        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->trans('Settings', [], 'Modules.M4puserlocation.Admin'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->trans('Show the country selector', [], 'Modules.M4puserlocation.Admin'),
                    'name' => 'M4P_USERLOCATION_ACTIVE',
                    'is_bool' => true,
                    'desc' => $this->trans('Puts the selector in the shop header.', [], 'Modules.M4puserlocation.Admin'),
                    'values' => $switchValues,
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Where to show it', [], 'Modules.M4puserlocation.Admin'),
                    'name' => 'M4P_USERLOCATION_PLACEMENT',
                    'desc' => $this->trans('Some themes render the navigation hooks in a hidden container, and the selector is then in the page but invisible. Switch to the top of the header if you see nothing.', [], 'Modules.M4puserlocation.Admin'),
                    'options' => [
                        'query' => [
                            [
                                'id' => self::PLACEMENT_NAV,
                                'name' => $this->trans('Header navigation (displayNav2)', [], 'Modules.M4puserlocation.Admin'),
                            ],
                            [
                                'id' => self::PLACEMENT_BANNER,
                                'name' => $this->trans('Top of the header (displayBanner)', [], 'Modules.M4puserlocation.Admin'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Ask on the first visit', [], 'Modules.M4puserlocation.Admin'),
                    'name' => 'M4P_USERLOCATION_POPUP',
                    'is_bool' => true,
                    'desc' => $this->trans('Shows a dialog until the visitor picks a country. With this off they keep the shop default and can still change it in the header.', [], 'Modules.M4puserlocation.Admin'),
                    'values' => $switchValues,
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Modules.M4puserlocation.Admin'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->trans('Save', [], 'Modules.M4puserlocation.Admin'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->trans('Back to list', [], 'Modules.M4puserlocation.Admin'),
            ],
        ];

        $fieldsValue = [];
        foreach (self::CONFIG_KEYS as $key) {
            $fieldsValue[$key] = Tools::getValue($key, Configuration::get($key));
        }

        $helper->tpl_vars = [
            'fields_value' => $fieldsValue,
            'languages' => $this->context->controller->getLanguages(),
        ];

        return $helper->generateForm($fields_form);
    }

    protected function isActive()
    {
        return (bool) Configuration::get('M4P_USERLOCATION_ACTIVE');
    }

    protected $countries;

    /** @return array countries enabled in this shop */
    public function getCountries()
    {
        if ($this->countries === null) {
            $this->countries = Country::getCountries((int) $this->context->language->id, true);
        }

        return $this->countries;
    }

    /** @return int the country the visitor picked, or 0 */
    public function getChosenCountry()
    {
        $id = (int) $this->context->cookie->{self::COOKIE_KEY};

        if (!$id) {
            return 0;
        }

        // A country switched off since the visitor chose it must not keep driving prices.
        return $this->isCountryEnabled($id) ? $id : 0;
    }

    protected function isCountryEnabled($idCountry)
    {
        foreach ($this->getCountries() as $country) {
            if ((int) $country['id_country'] === (int) $idCountry) {
                return true;
            }
        }

        return false;
    }

    /**
     * The core reads $context->country when the visitor has no address yet, so
     * setting it here is all that is needed for prices to follow the choice.
     * Once a cart carries an address, FrontController overwrites this again.
     */
    public function hookActionFrontControllerInitBefore()
    {
        if (!$this->isActive()) {
            return;
        }

        $id = $this->getChosenCountry();

        if ($id) {
            $this->context->country = new Country($id, (int) $this->context->language->id);
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->context->controller->registerStylesheet(
            'module-m4p-userlocation',
            'modules/' . $this->name . '/views/css/front.css',
            ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->registerJavascript(
            'module-m4p-userlocation',
            'modules/' . $this->name . '/views/js/front.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    /**
     * Tax rate per country, or an empty array when no single rate would be true.
     *
     * A rate belongs to a tax rules group, not to the shop, so it is only shown
     * when every active product shares one group. It is also pointless when the
     * visitor's group is set to net prices, because nothing on the page moves.
     *
     * @return array<int, float> id_country => rate
     */
    public function getTaxRates()
    {
        if (!Configuration::get('PS_TAX') || !$this->showsTaxIncludedPrices()) {
            return [];
        }

        $groups = Db::getInstance()->executeS(
            (new DbQuery())
                ->select('DISTINCT p.id_tax_rules_group')
                ->from('product', 'p')
                ->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop = ' . (int) $this->context->shop->id)
                ->where('ps.active = 1')
        );

        if (count((array) $groups) !== 1) {
            return [];
        }

        $rows = Db::getInstance()->executeS(
            (new DbQuery())
                ->select('tr.id_country, t.rate')
                ->from('tax_rule', 'tr')
                ->innerJoin('tax', 't', 't.id_tax = tr.id_tax AND t.active = 1')
                ->where('tr.id_tax_rules_group = ' . (int) $groups[0]['id_tax_rules_group'])
        );

        $rates = [];
        foreach ((array) $rows as $row) {
            $rates[(int) $row['id_country']] = (float) $row['rate'];
        }

        return $rates;
    }

    /** Country name, with its tax rate appended when one can be stated truthfully. */
    protected function countryLabel(array $country, array $rates)
    {
        $id = (int) $country['id_country'];

        if (!isset($rates[$id])) {
            return $country['name'];
        }

        // 23.000 reads as 23, 8.500 as 8.5.
        $rate = rtrim(rtrim(number_format($rates[$id], 3, '.', ''), '0'), '.');

        $name = $country['name'];

        return $this->trans('%country% — %rate% VAT', ['%country%' => $name, '%rate%' => $rate . '%'], 'Modules.M4puserlocation.Shop');
    }

    protected function showsTaxIncludedPrices()
    {
        $group = (int) Group::getCurrent()->id;

        return Group::getPriceDisplayMethod($group) == Group::PRICE_DISPLAY_METHOD_TAX_INCL;
    }

    public function hookDisplayNav2()
    {
        return $this->renderAt(self::PLACEMENT_NAV);
    }

    public function hookDisplayBanner()
    {
        return $this->renderAt(self::PLACEMENT_BANNER);
    }

    protected function renderAt($placement)
    {
        if (!$this->isActive() || Configuration::get('M4P_USERLOCATION_PLACEMENT') !== $placement) {
            return '';
        }

        $countries = $this->getCountries();

        if (count($countries) < 2) {
            return '';
        }

        $chosen = $this->getChosenCountry();
        $rates = $this->getTaxRates();

        foreach ($countries as &$country) {
            $country['label'] = $this->countryLabel($country, $rates);
        }
        unset($country);

        $this->context->smarty->assign([
            'm4p_userlocation_countries' => $countries,
            'm4p_userlocation_chosen' => $chosen ?: (int) $this->context->country->id,
            'm4p_userlocation_action' => $this->context->link->getModuleLink($this->name, 'country', [], true),
            'm4p_userlocation_token' => Tools::getToken(false),
            'm4p_userlocation_ask' => !$chosen && (bool) Configuration::get('M4P_USERLOCATION_POPUP'),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/selector.tpl');
    }
}
