{if isset($statusMessage) && mb_strlen($statusMessage) !== 0}
    <div class="Status">
        <p class="Status-message"><i class="fa fa-check"></i>{$statusMessage}</p>
    </div>
{/if}
