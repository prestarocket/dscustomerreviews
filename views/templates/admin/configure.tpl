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
	{if isset($comments) && $comments != null}
		<form method=POST>
		<input type='hidden' name='sliderAprrove'>
		<div class='form-wrapper'>
			<table class='table'>
				<thead class='thead-default'>
					<tr class="column-headers">
						<th scope="col">#</th>
						<th scope="col">{l s='Customer name' mod='customerreviews'}</th>
						<th scope="col">{l s='Stars' mod='customerreviews'}</th>
						<th scope="col">{l s='Reviews' mod='customerreviews'}</th>
						<th scope="col">{l s='Product  name' mod='customerreviews'}</th>
						<th scope="col">{l s='Date Add' mod='customerreviews'}</th>
						<th scope="col">{l s='Aprroved' mod='customerreviews'}</th>
						<th scope="col">{l s='Slider' mod='customerreviews'}</th>
						<th scope="col">{l s='Delete' mod='customerreviews'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$comments key=i item=$comment}
						<input type='hidden' name='commentId[{$comment.id_customerreviews}]' value='{$comment.id_customerreviews}'>
						<tr>
							<th>{$i+1}</th>
							<td>{$comment.firstname} {$comment.lastname}</td>
							<td>{$comment.stars}</td>
							<td>{$comment.content}</td>
							<td>{$comment.product_name}</td>
							<td>{$comment.timeadded}</td>
							<td>
								<span class="switch prestashop-switch fixed-width-md">
									<input type="radio" name="visible[{$comment.id_customerreviews}]" id='visible{$comment.id_customerreviews}_on' value="1" {if $comment.visible == 1}  checked="checked"{/if}>
									<label for="visible{$comment.id_customerreviews}_on">Tak</label>
									<input type="radio" name="visible[{$comment.id_customerreviews}]" id='visible{$comment.id_customerreviews}_off' value="0" {if $comment.visible == 0}  checked="checked"{/if}>
									<label for="visible{$comment.id_customerreviews}_off">Nie</label>
									<a class="slide-button btn"></a>
								</span>
							</td>
							<td>
								<span class="switch prestashop-switch fixed-width-md">
									<input type="radio" name="slider[{$comment.id_customerreviews}]" id='slider{$comment.id_customerreviews}_on' value="1" {if $comment.slider == 1}  checked="checked"{/if}>
									<label for="slider{$comment.id_customerreviews}_on">Tak</label>
									<input type="radio" name="slider[{$comment.id_customerreviews}]" id='slider{$comment.id_customerreviews}_off' value="0" {if $comment.slider == 0}  checked="checked"{/if}>
									<label for="slider{$comment.id_customerreviews}_off">Nie</label>
									<a class="slide-button btn"></a>
								</span>
							</td>
							<td>
								<input type='checkbox' name='delete[{$comment.id_customerreviews}]' class='form-control'>
							</td>
						</tr>
					{/foreach}
				<tbody>
			</table>
		</div>
			<div class='panel-footer'>
				<button class='btn btn-default pull-right'><i class="process-icon-save"></i>{l s='Save' mod='customerreviews'}</button>
			</div>
		</form>
	{else}
		{l s='No comments' mod='customerreviews'}
	{/if}
</div>

<div class="panel">
	<h3><i class="icon icon-tags"></i> {l s='Slider order' mod='customerreviews'}</h3>
	{if isset($slider) && $slider != null}
		<form method=POST>
			<div class='form-wrapper'>
				<table class='table'>
					<thead class='thead-default'>
						<tr class="column-headers">
							<th scope='col'></th>
							<th scope="col">{l s='Position' mod='customerreviews'}</th>
							<th scope="col">{l s='Customer name' mod='customerreviews'}</th>
							<th scope="col">{l s='Stars' mod='customerreviews'}</th>
							<th scope="col">{l s='Reviews' mod='customerreviews'}</th>				
						</tr>
					</thead>
					<tbody>					
						<input type='hidden' name='sliderOrder'>					
						{foreach $slider as $item}
							<tr id='{$item.id_customerreviews}'>
								<td class='js-drag-handle'>
									<div class='position-drag-handle' data-id='{$item.id_customerreviews}' data-position='{$item.sliderweight}' data-update-url='' data-update-method='POST'>
										<i class="material-icons">drag_indicator</i>
									</div>
								</td>
								<td>{$item.sliderweight}</td>
								<td>{$item.firstname} {$item.lastname}</td>
								<td>{$item.stars}</td>
								<td>{$item.content}</td>					
							</tr>
							{/foreach}								
					</tbody>
				</table>
			</div>
			<div class='panel-footer'>
				<button class='btn btn-default pull-right'><i class="process-icon-save"></i>{l s='Save' mod='customerreviews'}</button>
			</div>
		</form>
	{else}
		{l s='No comments in slider' mod='customerreviews'}
	{/if}	
</div>

<div class='panel'>
	<h3><i class="icon icon-tags"></i> {l s='Status included' mod='customerreviews'}</h3>
	<small>{l s='Select which status allow to write review to customer' mod='customerreviews'}</small>
	{if isset($statuses) && $statuses != null}
		<form method=POST>
			<input type='hidden' name='includedStatuses'>
			<div class='form-wrapper'>
				<table class='table'>
					<thead class='thead-default'>
						<tr class="column-headers">
							<th>#</th>
							<th>{l s='Status name' mod='customerreviews'}</th>
							<th>{l s='Active' mod='customerreviews'}
						</tr>
					</thead>
					<tbody>
						{foreach from=$statuses key=i item=$status}
						<input type='hidden' name='statusid[{$status.id_status}]' value='status[{$status.id_status}]'>
							<tr>
								<th>{$i+1}</th>
								<td>{$status.status_name}</td>
								<td>
									<span class="switch prestashop-switch fixed-width-md">
										<input type="radio" name="status[{$status.id_status}]" id='status{$status.id_status}_on' value="1" {if $status.active == 1}  checked="checked"{/if}>
										<label for="status{$status.id_status}_on">Tak</label>
										<input type="radio" name="status[{$status.id_status}]" id='status{$status.id_status}_off' value="0" {if $status.active == 0 || $status.active == null}  checked="checked"{/if}>
										<label for="status{$status.id_status}_off">Nie</label>
										<a class="slide-button btn"></a>
									</span>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			<div class='panel-footer'>
				<button class='btn btn-default pull-right'><i class="process-icon-save"></i>{l s='Save' mod='customerreviews'}</button>
			</div>
		</form>
	{else}
		{l s='Any status find' mod='customerreviews'}
	{/if}
</div>