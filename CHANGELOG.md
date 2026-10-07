# Changelog

All notable changes to this module are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the module uses
[semantic versioning](https://semver.org/).

## [2.0.0] - 2026-10-06

First open-source release, published under the MIT license. The module was a prototype that could
not run on a fresh shop, so this release rebuilds it on PrestaShop's own country and tax data.

### Changed

- The country the visitor picks is set on the PrestaShop context, so the core works out the tax and
  formats the price. The module keeps no country or rate data of its own and performs no arithmetic.
- The choice is kept in the signed PrestaShop cookie and validated against the shop's enabled
  countries; a country switched off afterwards falls back to the shop default.
- The selector renders through a hook — `displayNav2` or `displayBanner`, whichever the configuration
  picks — so it does not depend on any theme's markup.
- Runs on PrestaShop 9: `ps_versions_compliancy` covers it.
- Every string, country name included, comes from the translation catalogues.

### Added

- A configuration page with an on/off switch, a choice of placement and a switch for the first-visit
  dialog, listing the countries the selector will offer and warning when the shop has only one enabled.
- A choice between the `displayNav2` and `displayBanner` hooks. Themes are free to put the navigation
  hooks in a hidden container, and the selector is then in the page but invisible.
- The selector works without JavaScript: the submit button sits in a `<noscript>`, so a browser with
  scripting never renders it and picking an option submits on its own.
- A country switched off in the shop after a visitor chose it falls back to the shop default.
- The selector is captioned "Prices for" and each option carries its tax rate, so it does not read as
  a language switcher. The rate is shown only when one tax rules group covers every active product and
  the visitor's group displays prices with tax; otherwise the options are plain country names.
- A warning in the configuration naming customer groups set to display prices without tax, whose
  members will not see prices move when they pick a country.
- Complete en-US and pl-PL XLIFF catalogues.

### Changed

- The choice is kept in the PrestaShop cookie, which is signed, instead of a cookie written by
  JavaScript, and it is validated against the shop's enabled countries on the way in.
- The redirect back after choosing is rebuilt on the shop base, so a crafted `back` parameter cannot
  send a visitor to another site.

### Removed

- `m4p_userlocation_countries` and everything that read it — PrestaShop already stores countries and
  their tax rules.
- The `displayProductPriceBlock` hook and `price.tpl`. Nothing needs to render: the core prices
  follow the context country on their own.
