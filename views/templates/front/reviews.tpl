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
{capture name=path}{l s='My comments' mod='customerreviews'}{/capture}
<div class='card card-block'>
    <h3 class='page-subheading'>{l s='Comments settings' mod='customerreviews'}</h3>
    <div class='col-lg-4'>
        <form class='form'>
            <input type='hidden' name='token' value=''>
            <input type='hidden' name='customerReviewsSettings'>
            <div class='form-group'>

            </div>
            <button type='submit' class='btn btn-primary'>{l s='Update settings' mod='customerreviews'}</button>
        </form>
    </div>

    <div class='col-lg-8'>
        <h4>{l s='Your comments' mod='customerreviews'}</h4>
        {foreach $comments as $comment}
            <div class='reviews'>
                <div class='reviewGroup'>
                    <div class='starsGroup'>
                        {if $comment.stars == 5}

                        {elseif $comment.stars == 4}

                        {elseif $comment.stars == 3}

                        {elseif $comment.stars == 2}

                        {elseif $comment.stars == 1}

                        {/if}
                    </div>
                    <div class='date'>
                        {$comment.date}
                    </div>
                </div>
                <div class='reviewContent'>
                    {$comment.content}
                </div>
            </div>
        {/foreach}
    </div>
</div>