{include file="header.tpl"}
{include file="navigation.tpl"}
<main class="Site-content">
    <section class="Section">
        <div class="Container">
            {include file="errorMessages.tpl"}
            {include file="statusMessage.tpl"}
                <form action="{$smarty.server.SCRIPT_NAME}" method="post">
                    <div class="InputCombo Grid-full">
                        <label for="{$search->getName()}" class="InputCombo-label">Search:</label>
                        <input type="search" id="{$search->getName()}" name="{$search->getName()}" value="{$search->getValue()}" class="InputCombo-field">
                        <button type="submit" class="Button"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </form>
            <br>
            <h2 class="Section-heading">List of Products</h2>
            <div class="InputCombo Grid-full">
                <form action="{$smarty.server.SCRIPT_NAME}" method="post"  enctype="multipart/form-data">
                    {include file="tablestyles.tpl"}
                    <table>
                        <tr>
                            <!-- statt Links wäre eine Pulldown Box im vierten <th> mit mehreren Auswahlmöglichkeien denkbar -->
                            <th><a href="{$smarty.server.SCRIPT_NAME}?{$sortKey}=pid&amp;">PID</a></th>
                            <th><a href="{$smarty.server.SCRIPT_NAME}?{$sortKey}=pname&amp;">Product_name</a></th>
                            <th><a href="{$smarty.server.SCRIPT_NAME}?{$sortKey}=price&amp;">Price</a></th>
                            <th>&nbsp;</th>
                        </tr>
                        {foreach key=cid item=con from=$pageArray}
                            <tr>
                                <td>{$con.idproduct}</td>
                                <td>{$con.product_name}</td>
                                <td>{$con.price}</td>
                                <!-- Einige Shopsysteme lösen die Bestellung über einen GET-Aufruf. Wir wollen aber die TNormform verwenden, die auf POST aufbaut
                                <!-- <td><a href="index.php?pid={$con.idproduct}">Add to Cart</a></td> -->
                                <td><button name="pid[{$con.idproduct}]" type="submit"><i class="fa fa-cart-plus" aria-hidden="true">&nbsp;Add To Cart</button></td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td> No products found in search </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        {/foreach}
                    </table>
                    {include file="pagination.tpl"}
                </form>
             </div>
        </div>
    </section>
</main>
{include file="footer.tpl"}