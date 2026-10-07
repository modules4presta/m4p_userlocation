{**
 * m4p_userlocation
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 *}
<div class="m4p-userlocation" data-m4p-userlocation>
	<form class="m4p-userlocation__pill" method="post" action="{$m4p_userlocation_action|escape:'html':'UTF-8'}">
		<input type="hidden" name="token" value="{$m4p_userlocation_token|escape:'html':'UTF-8'}">
		<input type="hidden" name="back" value="{$urls.current_url|escape:'html':'UTF-8'}">

		<label class="m4p-userlocation__caption" for="m4p-userlocation-country">
			{l s='Prices for' d='Modules.M4puserlocation.Shop'}
		</label>

		<select class="m4p-userlocation__select" id="m4p-userlocation-country" name="id_country" data-m4p-userlocation-select>
			{foreach from=$m4p_userlocation_countries item=country}
				<option value="{$country.id_country|intval}"{if $country.id_country == $m4p_userlocation_chosen} selected{/if}>{$country.label|escape:'html':'UTF-8'}</option>
			{/foreach}
		</select>

		{* Only reachable without scripting; with it, picking an option submits. *}
		<noscript>
			<button type="submit" name="m4p_userlocation_submit" value="1" class="m4p-userlocation__apply">
				{l s='Change' d='Modules.M4puserlocation.Shop'}
			</button>
		</noscript>
	</form>

	{if $m4p_userlocation_ask}
		<div class="m4p-userlocation__overlay" data-m4p-userlocation-dialog>
			<div class="m4p-userlocation__dialog" role="dialog" aria-modal="true" aria-labelledby="m4p-userlocation-title">
				<h2 class="m4p-userlocation__title" id="m4p-userlocation-title">{l s='Where are you shopping from?' d='Modules.M4puserlocation.Shop'}</h2>
				<p class="m4p-userlocation__lead">{l s='We use it to show prices with the tax that applies to you.' d='Modules.M4puserlocation.Shop'}</p>

				<form method="post" action="{$m4p_userlocation_action|escape:'html':'UTF-8'}">
					<input type="hidden" name="token" value="{$m4p_userlocation_token|escape:'html':'UTF-8'}">
					<input type="hidden" name="back" value="{$urls.current_url|escape:'html':'UTF-8'}">

					<label class="m4p-userlocation__dialog-label" for="m4p-userlocation-country-dialog">
						{l s='Prices for' d='Modules.M4puserlocation.Shop'}
					</label>

					<select class="form-control" id="m4p-userlocation-country-dialog" name="id_country">
						{foreach from=$m4p_userlocation_countries item=country}
							<option value="{$country.id_country|intval}"{if $country.id_country == $m4p_userlocation_chosen} selected{/if}>{$country.label|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>

					<button type="submit" name="m4p_userlocation_submit" value="1" class="btn btn-primary m4p-userlocation__confirm">
						{l s='Confirm' d='Modules.M4puserlocation.Shop'}
					</button>
				</form>
			</div>
		</div>
	{/if}
</div>
