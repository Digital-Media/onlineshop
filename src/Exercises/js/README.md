# Using AJAX for sending a POST-Request 

The Templates onlineshop/templates/*Main.html.twig already contain a link for using an external javascript file.
To use it, you have to uncomment the line above the include of the footer.

In onlineshop/src/DBAccess you find the Example DBAjaxDemo.php and in the subdirectory js two files with an AJAX implementation.
One shows a solution with plain javascript. The other shows a solution using jquery.

There are several other ways to solve this problem. You can send a plainObject or a string, instead of the FormData object.
You can use innerHTML instead of building the whole DOM nodes with javascript functions.
In some cases (mycart, index) you will have to register other events than submit, to send only one entry of the form. 
For these examples it will be a better solution not sending the whole form in case of using AJAX.