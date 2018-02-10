{include file="header.tpl"}
{include file="navigation.tpl"}
<main class="Site-content">
    <section class="Section">
        <div class="Container">
            <h2 class="Section-heading">Register for an OnlineShop account</h2>
            {include file="errorMessages.tpl"}
            {include file="statusMessage.tpl"}
            <form action="{$smarty.server.SCRIPT_NAME}" method="post" enctype="multipart/form-data">
                <div class="InputCombo Grid-full">
                    <label for="{$firstname->getName()}" class="InputCombo-label">Firstname:</label>
                    <input type="text" id="{$firstname->getName()}" name="{$firstname->getName()}" value="{$firstname->getValue()}" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$lastname->getName()}" class="InputCombo-label">Lastname:</label>
                    <input type="text" id="{$lastname->getName()}" name="{$lastname->getName()}" value="{$lastname->getValue()}" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$nickname->getName()}" class="InputCombo-label">Nickname:</label>
                    <input type="text" id="{$nickname->getName()}" name="{$nickname->getName()}" value="{$nickname->getValue()}" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$email->getName()}" class="InputCombo-label">Email:</label>
                    <input type="email" id="{$email->getName()}" name="{$email->getName()}" value="{$email->getValue()}" class="InputCombo-field">
                </div>
                <p> Format: +43 732 1234-1234 </p>
                <div class="InputCombo Grid-full">
                    <label for="{$phone->getName()}" class="InputCombo-label">Phone:</label>
                    <input type="text" id="{$phone->getName()}" name="{$phone->getName()}" value="{$phone->getValue()}" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$mobile->getName()}" class="InputCombo-label">Mobile:</label>
                    <input type="text" id="{$mobile->getName()}" name="{$mobile->getName()}" value="{$mobile->getValue()}" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$fax->getName()}" class="InputCombo-label">Fax:</label>
                    <input type="text" id="{$fax->getName()}" name="{$fax->getName()}" value="{$fax->getValue()}" class="InputCombo-field">
                </div>
                <p> Use only letters, numbers, and the underscore. Must be between {$smarty.const.PWDMIN} and {$smarty.const.PWDMAX} characters long. </p>
                <div class="InputCombo Grid-full">
                    <label for="{$password->getName()}" class="InputCombo-label">Password:</label>
                    <input type="password" id="{$password->getName()}" name="{$password->getName()}" class="InputCombo-field">
                </div>
                <div class="InputCombo Grid-full">
                    <label for="{$passwordrepeat->getName()}" class="InputCombo-label">Repeat Password:</label>
                    <input type="password" id="{$passwordrepeat->getName()}" name="{$passwordrepeat->getName()}" class="InputCombo-field">
                </div>
                <div class="Grid-full">
                    <button type="submit" class="Button">Create my account</button>
                </div>
            </form>
        </div>
    </section>
    <section class="Section">
        <div class="Container">
            <h2 class="Section-heading">Already registered<i class="fa fa-question"></i></h2>
            <p>Use your existing OnlineShop account to login <a href="login.php">here</a></p>
        </div>
    </section>
</main>
{include file="footer.tpl"}