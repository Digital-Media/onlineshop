{include file="header.tpl"}
{include file="navigation.tpl"}
<main class="Site-content">
    <section class="Section">
        <div class="Container">
            <h2 class="Section-heading">My Cart</h2>
            {include file="errorMessages.tpl"}
            {include file="statusMessage.tpl"}
            <form action="{$smarty.server.SCRIPT_NAME}" method="post" enctype="multipart/form-data">
                <div class="InputCombo Grid-full">
                    {include file="tablestyles.tpl"}
                    <table>
                        <tr>
                            <th>PID</th>
                            <th>Product_name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                        </tr>
                        {foreach key=cid item=con from=$pageArray}
                            <tr>
                                <td>{$con.product_idproduct}</td>
                                <td>{$con.product_name}</td>
                                <td>{$con.price}</td>
                                <td><input type="text" name="quantity[{$con.product_idproduct}]" size="10" maxlength="10" value="{$con.quantity}"/></td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td> No Products in Cart </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
                {if count($pageArray) !== 0 }
                    <div class="Grid-full">
                        <input type="submit" name="update" value="Update Cart" />
                        <input type="submit" name="checkout" value="Go To Checkout" />
                    </div>
                {/if}
            </form>
        </div>
    </section>
</main>
{include file="footer.tpl"}
