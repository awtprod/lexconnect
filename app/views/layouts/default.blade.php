<html>
<head>
@yield('head')
<meta charset="utf-8">
<style>
table {
    width:100px;
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;
}
table#t01 tr:nth-child(even) {
    background-color: #eee;
}
table#t01 tr:nth-child(odd) {
   background-color:#fff;
}
table#t01 th	{
    background-color: black;
    color: white;
}
</style>
</head>
<body>
@if(Auth::check())
@if(Auth::user()->user_role == 'Client')
@if(Auth::user()->role == 'Employee')
<div>
{{ link_to("/home/", 'Home') }}{{ link_to("/orders/", 'Orders') }}{{ link_to("/invoices/", 'Invoices') }}{{ link_to("/logout/", 'Logout') }}
</div>
@else
<div>
{{ link_to("/home/", 'Home') }}{{ link_to("/orders/", 'Orders') }}{{ link_to("/invoices/", 'Invoices') }}{{ link_to("/users/", 'Users') }}{{ link_to("/logout/", 'Logout') }}
</div>
@endif
@elseif(Auth::user()->user_role == 'Vendor')
<div>
{{ link_to("/home/", 'Home') }}{{ link_to("/jobs/", 'Jobs') }}{{ link_to("/invoices/", 'Invoices') }}{{ link_to("/users/", 'Users') }}{{ link_to("/logout/", 'Logout') }}
</div>
@elseif(Auth::user()->user_role == 'Admin')
<div>
{{ link_to("/home/", 'Home') }}{{ link_to("/jobs/", 'Jobs') }}{{ link_to("/orders/", 'Orders') }}{{ link_to("/invoices/", 'Invoices') }}{{ link_to("/users/", 'Users') }}{{ link_to("/logout/", 'Logout') }}
</div>
@endif
@endif
@yield('content')

</body>
</html>
