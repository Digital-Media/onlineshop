{include file="header.tpl"}
{include file="navigation.tpl"}
<main class="Site-content">
    <section class="Section">
        <div class="Container">
            <h2 class="Section-heading">Add Product</h2>
            {include file="errorMessages.tpl"}
            {include file="statusMessage.tpl"}
            <form action="{$smarty.server.SCRIPT_NAME}" method="post" enctype="multipart/form-data">
                <div class="InputCombo Grid-full">
                    <label for="{$pname->getName()}" class="InputCombo-label">Product Name:</label>
                    <input type="text" id="{$pname->getName()}" name="{$pname->getName()}" value="{$pname->getValue()}" placeholder="Choose a unique Product Name" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$price->getName()}" class="InputCombo-label">Price:</label>
                    <input type="text" id="{$price->getName()}" name="{$price->getName()}" value="{$price->getValue()}" size="10" maxlength="10"  placeholder="0,00" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$ptype->getName()}" class="InputCombo-label">Product Type:</label>
                    <select name="{$ptype->getName()}" id="{$ptype->getName()}" size="1" class="InputCombo-field">
                        {foreach key=cid item=con from=$ptypeArray}
                            <option {if isset($selected)}{if $selected === $con.product_category_name} selected="selected" {/if} {/if}>{$con.product_category_name}</option>
                            {foreachelse}
                            <option>Nothing to select so far</option>
                        {/foreach}
                    </select>
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$active->getName()}" class="InputCombo-label">Active</label><span>&nbsp;</span>
                    <input type="radio" name="{$active->getName()}"  value="1" {if $active->getValue() == 1} checked="checked" {/if}><span>&nbsp;</span>
                    <label for="{$active->getName()}" class="InputCombo-label">Inactive</label><span>&nbsp;</span>
                    <input type="radio" name="{$active->getName()}"  value="0" {if $active->getValue() == 0}  checked="checked" {/if}><span>&nbsp;</span>
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$shortdesc->getName()}" class="InputCombo-label">Short Description:</label>
                    <textarea name="{$shortdesc->getName()}" id="{$shortdesc->getName()}" cols="40" rows="5"  class="InputCombo-field">{$shortdesc->getValue()}</textarea>
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$longdesc->getName()}" class="InputCombo-label">Long Description:</label>
                    <textarea name="{$longdesc->getName()}" id="{$longdesc->getName()}" cols="40" rows="15"  class="InputCombo-field">{$longdesc->getValue()}</textarea>
                </div>
                <div class="Grid-full">
                    <button type="submit" class="Button">Add Product</button>
                </div>
            </form>
        </div>
    </section>
</main>
{include file="footer.tpl"}
