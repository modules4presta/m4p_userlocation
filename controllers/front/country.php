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

class m4p_userlocationCountryModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (!Tools::isSubmit('m4p_userlocation_submit')) {
            $this->back();
        }

        if (!hash_equals(Tools::getToken(false), (string) Tools::getValue('token'))) {
            $this->back();
        }

        $id = (int) Tools::getValue('id_country');

        foreach ($this->module->getCountries() as $country) {
            if ((int) $country['id_country'] === $id) {
                // Stored in the PrestaShop cookie, which is signed, so the choice
                // cannot be edited into a country the shop does not sell to.
                $this->context->cookie->{m4p_userlocation::COOKIE_KEY} = $id;
                $this->context->cookie->write();

                break;
            }
        }

        $this->back();
    }

    /**
     * Returns to the page the visitor came from.
     *
     * Only an address already inside this shop is accepted, so a crafted back
     * parameter cannot send the visitor elsewhere. It has to stay absolute:
     * Tools::redirect reads a relative path as a page name and runs it through
     * getPageLink, which turns a product URL into a broken controller link.
     */
    protected function back()
    {
        $base = $this->context->shop->getBaseURL(true);
        $back = (string) Tools::getValue('back');

        if ($back !== '' && strpos($back, $base) === 0) {
            Tools::redirect($back);
        }

        Tools::redirect($base);
    }
}
