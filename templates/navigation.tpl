<!-- Styles not needed for IMAR, therefore not in css. So its easier to reuse IMAR -->
<style type="text/css" scoped>
    {literal}
        .Navigation {
            text-align: left;
        }
    {/literal}
</style>
<div class="Header Navigation">
    <nav class="Container">
        <span class="u-spaceRS" > {if !($smarty.server.SCRIPT_NAME === "/onlineshop/index.php")} <a href="index.php">Home</a> {/if} </span>
        <span class="u-spaceRS" > {if !($smarty.server.SCRIPT_NAME === "/onlineshop/register.php")} <a href="register.php">Register</a> {/if} </span>
        <span class="u-spaceRS" > {if !($smarty.server.SCRIPT_NAME === "/onlineshop/product.php")} <a href="product.php">Add Products</a> {/if} </span>
        <span class="u-spaceRS" > {if !($smarty.server.SCRIPT_NAME === "/onlineshop/mycart.php")} <a href="mycart.php">My Cart</a> {/if} </span>
        <span class="u-spaceRS" > {if !($smarty.server.SCRIPT_NAME === "/onlineshop/dbdemo.php")} <a href="dbdemo.php">DEMO</a> {/if} </span>
    </nav>
</div>
