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
* to license@dark-side.pro so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.dark-side.pro for more information.
*
*  @author    Dark-Side.pro <contact@dark-side.pro>
*  @copyright 2007-2019 Dark-Side.pro
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  
*}
{if isset($isneed[0])}
    {if $customer == true}
        <div id='add_review'>
            <form method=POST>
                <div class='rating'>
                    <input type='hidden' name='addReview'>
                    <input type='hidden' name='id_order_detail' value='{$isneed[0]["id_order_detail"]}'>
                    <label>
                        <input type="radio" name="stars" value="1" required/>
                        <span class="icon">★</span>
                    </label>
                    <label>
                        <input type="radio" name="stars" value="2" required/>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                    </label>
                    <label>
                        <input type="radio" name="stars" value="3" required/>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>   
                    </label>
                    <label>
                        <input type="radio" name="stars" value="4" required/>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                    </label>
                    <label>
                        <input type="radio" name="stars" value="5" required/>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                    </label>
                </div>
                <div class='form-group'>
                    <label for='reviews'>{l s='Twój komentarz' mod='customerreviews'}</label>
                    <textarea class='form-control' name='reviews' maxlength="512" minlength="2" rows='3' required></textarea>
                </div>
                <button type='submit' class='btn btn-default'>{l s='Dodaj ocenę' mod='customerreviews'}</button>
            </form>
        </div>
    {/if}
{else}

{/if}


<div id='reviews'>
    <ul>
        {foreach $reviews as $review}
            <li>
                {$review.date}
                {$review.firstname} {$reviews.lastname} 
                {if $reviews.stars == 5}

                {elseif $reviews.stars == 4}

                {elseif $reviews.stars == 3}

                {elseif $reviews.stars == 2}

                {elseif $reviews.stars == 1}

                {/if}
                {$review.stars}
                {$review.content}
            </li>
        {/foreach}
    </ul>
</div>