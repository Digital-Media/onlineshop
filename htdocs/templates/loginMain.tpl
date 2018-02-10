{include file="header.tpl"}
{include file="navigation.tpl"}
<main class="Site-content">
    <section class="Section">
        <div class="Container">
            <h2 class="Section-heading">Login</h2>
            {include file="errorMessages.tpl"}
            {include file="statusMessage.tpl"}
            <form action="{$smarty.server.SCRIPT_NAME}" method="post" enctype="multipart/form-data">
                <div class="InputCombo Grid-full">
                    <label for="{$email->getName()}" class="InputCombo-label">Email:</label>
                    <input type="text" id="{$email->getName()}" name="{$email->getName()}" value="{$email->getValue()}" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$password->getName()}" class="InputCombo-label">Password:</label>
                    <input type="password" id="{$password->getName()}" name="{$password->getName()}" class="InputCombo-field">
                </div>
                <div class="Grid-full">
                    <button type="submit" class="Button">Log me in</button>
                </div>
            </form>
        </div>
    </section>
    <section class="Section">
        <div class="Container">
            <h2 class="Section-heading">No account<i class="fa fa-question"></i></h2>
            <p>Register your OnlineShop account <a href="register.php">here</a></p>
        </div>
    </section>
</main>
{include file="footer.tpl"}