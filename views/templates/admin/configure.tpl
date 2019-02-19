{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Customer Reviews' mod='customerreviews'}</h3>
	<table class='table'>
		<thead>
			<tr>
				<th>#</th>
				<th>{l s='Customer' name mod='customerreviews'}</th>
				<th>{l s='Stars' mod='customerreviews'}</th>
				<th>{l s='Reviews' mod='customerreviews'}</th>
				<th>{l s='Product' name mod='customerreviews'}</th>
				<td>{l s='Date Add' mod='customerreviews'}</th>
				<th>{l s='Aprroved' mod='customerreviews'}</th>
				<th>{l s='Slider' mod='customerreviews'}</th>
				<th>{l s='Actions' mod='customerreviews'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach $comments as $comment}
				<tr>
					<th>{$comment.customer}</th>
					<td>{$comment.stars}</td>
					<td>{$comment.comment}</td>
					<td></td>
					<td>{$comment.date}</td>
					<td>{$comment.approved}</td>
					<td>{$comment.slider}</td>
					<td></td>
				</tr>
			{/foreach}
		<tbody>
	</table>
</div>

<div class="panel">
	<h3><i class="icon icon-tags"></i> {l s='Slider order' mod='customerreviews'}</h3>

</div>
