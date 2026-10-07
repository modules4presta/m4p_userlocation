# Country selector for PrestaShop 8 & 9

**A visitor from Berlin sees your prices with Polish VAT until they create an account and type in an address — this module lets them say where they are, and the shop prices itself accordingly from the first page.**

> **Meta description (155 chars):** Let PrestaShop visitors pick their country so prices show the right tax before they have an address. Uses your own tax rules. Free, MIT licensed.

---

## Why the wrong tax on the first page matters for your store

PrestaShop works out tax from an address. Until a visitor has one, the shop falls back to its own country — which is correct for a domestic shop and wrong for everyone else. A German buyer on a Polish shop reads prices with 23% VAT, decides you are expensive, and leaves. Nothing on the page tells them the number is provisional.

That is a problem specific to the top of the funnel. By the time someone is in checkout the address exists and every price is right; the damage is done earlier, on the category page they bounced off. It is also invisible in your analytics: the visitor does not report a wrong price, they just do not come back.

For a B2B shop the same mechanism works the other way. A buyer who can see prices at their own rate before registering can compare your offer with their local supplier, which is the comparison you want them making.

- **Prices are right from the first page** — not from the first address.
- **The visitor controls it** — they say where they are rather than being guessed at by IP.
- **Your tax rules decide, not the module** — whatever you configured under International is what shows.

## What the module does

A country selector sits in the shop header, and on a first visit the module can ask once in a dialog. The chosen country is set on the PrestaShop context, which is exactly where the core looks when a visitor has no address yet, so prices, tax labels and totals follow your own tax rules. The module computes nothing itself and stores no catalogue data.

### Key features

- **Built on your shop's data** — the list is the countries you enabled under International → Locations → Countries, and rates come from your tax rules.
- **No JavaScript required** — picking an option submits on its own, and the fallback button lives in a `<noscript>` so it is never drawn and then taken away.
- **Reads as a price control, not a language switcher** — the field is captioned "Prices for" and each option carries its tax rate, so nobody mistakes it for a flag-based language menu.
- **Two placements** — the header navigation hook or the top of the header, because some themes render the navigation hooks in a hidden container.
- **Optional first-visit dialog** — ask once, or stay quiet and let people find the selector in the header.
- **Signed storage** — the choice is kept in PrestaShop's own cookie and checked against your enabled countries, so it cannot be edited into a country you do not sell to.
- **Self-correcting** — a country you later switch off stops applying, and visitors fall back to the shop default.

### When the rate is shown next to each country

The options read `Germany — 19% VAT` only when that figure is true for the whole catalogue: a rate belongs to a tax rules group, not to a shop, so it is shown only where every active product shares one group. In a shop that sells at several rates the options fall back to plain country names rather than quoting a number that is wrong for part of the catalogue.

It is also left out for customer groups you have set to display prices without tax — a common B2B arrangement. Those customers see net figures that no country can change, so advertising a rate beside them would be misleading. The configuration page names the groups this applies to.

### How it interacts with customer addresses

The choice only fills the gap before an address exists. As soon as a cart carries an address, PrestaShop sets the context country from that address and the selector no longer affects prices — which is right, because an address is a statement of fact and a header dropdown is a preference. Nothing the visitor picks here is carried into their order.

The module also does not change shipping, currency or language. It answers one question: which tax rate to show.

## Compatibility

| | |
|---|---|
| PrestaShop | 1.7.6 – 9.x |
| PHP | 7.2+ |
| Requirements | none — no external service, no geolocation database |
| Multistore | Compatible — each shop offers its own enabled countries |
| Themes | Renders in `displayNav2` or `displayBanner`, whichever you pick in the configuration |

The module overrides no core class and creates no database table. It stores two settings in `ps_configuration` and removes them when uninstalled.

## Installation

1. Upload and install the module from **Modules → Module Manager**.
2. Enable the countries you sell to under **International → Locations → Countries**, and check their rates under **International → Taxes → Tax Rules**.
3. Open the module configuration, choose where the selector goes and turn the first-visit dialog on or off.

If the selector does not appear, there are two usual reasons. The first is that only one country is enabled — with nothing to choose between, the module renders nothing, and the configuration page lists the countries it can see and says so when there is only one.

The second is the placement. Themes are free to render `displayNav1` and `displayNav2` wherever they like, and some put them in a container that is deliberately hidden — kept in the DOM so that module JavaScript keeps working, but sized to a single pixel. The selector is then in the page and invisible. Switching the placement to the top of the header moves it to `displayBanner`, which themes render as a visible strip above the navigation.

## Configuration options

| Setting | Description |
|---|---|
| **Show the country selector** | Turns the feature on and off without uninstalling it. |
| **Where to show it** | Header navigation (`displayNav2`) or the top of the header (`displayBanner`). Switch to the second one if your theme hides the navigation hooks. |
| **Ask on the first visit** | Shows a dialog until the visitor picks a country. With it off they keep the shop default and can still change it in the header. |

## Frequently asked questions

**Does this guess the country from the visitor's IP address?**
No. It asks. Guessing needs a geolocation database to keep current and gets it wrong for anyone travelling or behind a VPN, and PrestaShop already ships its own geolocation feature if that is what you want.

**Does the choice follow through to the order?**
No. Once there is an address on the cart, that address decides the tax, and the order is calculated from it like any other. The selector only covers the stretch before an address exists.

**Can a visitor use it to pay less tax?**
No. The choice changes what is displayed before checkout; the order is priced from the delivery address. The stored value is also checked against your enabled countries, so it cannot be edited into something you do not sell to.

**What if I only sell domestically?**
Then you do not need this module. With one country enabled it renders nothing.

---

**Keywords:** prestashop country selector, prestashop tax by country, prestashop vat display, prestashop b2b prices, show prices with local vat, prestashop context country

## License

MIT — see [LICENSE](LICENSE). Free to use commercially, fork and modify; keep the copyright notice.

## Contributing

Bug reports and pull requests are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md). For security
issues, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

---

Built by [Nice Code](https://nice-code.com/pl/produkty/sklep-b2b-prestashop) — we build and maintain PrestaShop stores.

© Nice Code sp. z o.o. (Modules4Presta) — released under the MIT license.
