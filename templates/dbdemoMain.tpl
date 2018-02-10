{include file="header.tpl"}
{include file="navigation.tpl"}
<main class="Site-content">
    <section class="Section">
        <div class="Container">
            {include file="errorMessages.tpl"}
            {include file="statusMessage.tpl"}
                <form action="{$smarty.server.SCRIPT_NAME}" method="post">
                    <div class="InputCombo Grid-full">
                        <label for="{$ptype->getName()}" class="InputCombo-label">Product Category:</label>
                        <input type="search" id="{$ptype->getName()}" name="{$ptype->getName()}" value="{$ptype->getValue()}" class="InputCombo-field">
                    </div>
                    <div class="Grid-full">
                        <button type="submit" class="Button">Add Product Category</button>
                    </div>
                </form>
            <br>
            <h2 class="Section-heading">List of Product Categories</h2>
            <div class="InputCombo Grid-full">
                    {include file="tablestyles.tpl"}
                    <table>
                        <tr>
                            <th>PTypeID</th>
                            <th>Product_Type</th>
                        </tr>
                        {foreach key=cid item=con from=$pageArray}
                            <tr>
                                <td>{$con.idproduct_category}</td>
                                <td>{$con.product_category_name}</td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td> No products found in search </td>
                                <td>&nbsp;</td>
                            </tr>
                        {/foreach}
                    </table>
             </div>
        </div>
    </section>
</main>
{include file="footer.tpl"}