<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OnlineShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,700">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{$smarty.const.CSS_DIR}/main.css">
</head>
<body class="Site">
<header class="Site-header">
    <div class="Header Header--small">
        <div class="Header-titles">
            <h1 class="Header-title"><i class="fa fa-shopping-bag" aria-hidden="true"></i>OnlineShop</h1>
            <p class="Header-subtitle">For Database Exercises</p>
        </div>
        {if isset($smarty.session.isloggedin)}
        <div class="Header-logout">
            You are logged in as  {$smarty.session.first_name} {$smarty.session.last_name}. <a href="logout.php" class="Button u-spaceLM">Logout</a>
        </div>
        {elseif session_id() != ""}
        <div class="Header-logout">
            <a href="login.php"  class="Button u-spaceLM">Login</a>
        </div>
        {/if}
    </div>
</header>