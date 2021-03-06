{*
 * 2017 Thirty Bees
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@thirtybees.com so we can send you a copy immediately.
 *
 *  @author    Thirty Bees <modules@thirtybees.com>
 *  @copyright 2017 Thirty Bees
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{capture name=path}
	{* TODO: remove direct call to BeesBlog *}
	<a href="{BeesBlog::GetBeesBlogLink('beesblog')}">{l s='Blog' mod='beesblog'}</a>
	{if $title_category != ''}
		<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{$title_category|escape:'htmlall':'UTF-8'}
	{/if}
{/capture}

{if $postcategory == ''}
	<p class="error">{l s='No posts with this tag' mod='beesblog'}</p>
{else}
	<div id="beesblogcat" class="block">
		{foreach from=$postcategory item=post}
			{include file="./category_loop.tpl" postcategory=$postcategory}
		{/foreach}
	</div>
{/if}
{if isset($beescustomcss)}
	<style>
		{$beescustomcss|escape:'htmlall':'UTF-8'}
	</style>
{/if}
