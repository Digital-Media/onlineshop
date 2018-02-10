<div id="Pagination">
    <hr>
    {if $pagecount > 1 }
        {if $current_page != 1 }<a href="{$smarty.server.SCRIPT_NAME}?{$startKey}={$startprevious}">Previous</a>
        {/if}
        {foreach from=$pagenumber item=i key=pageno}
            {if $pageno != $current_page }<a href="{$smarty.server.SCRIPT_NAME}?{$startKey}={$i}">{$pageno}</a>
            {else}
                {$pageno}
            {/if}
        {/foreach}
        {if $current_page != $pagecount }<a href="{$smarty.server.SCRIPT_NAME}?{$startKey}={$startnext}">Next</a>
        {/if}
    {/if}
</div>
