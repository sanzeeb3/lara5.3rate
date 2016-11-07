<!DOCTYPE html>
<html lang="en">
<head>
<title>Laravel Rate</title>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="{{asset('jquery.min.js')}}"></script>
<script src="{{asset('jquery.validate.min.js')}}"></script>
<link href="{{asset('bootstrap/css/bootstrap.css')}}" rel="stylesheet">
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<link href="{{asset('sweetalert.css')}}" rel="stylesheet">
<script src="{{asset('sweetalert.js')}}"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
</head>
<style>

</style>
<body>
<div class="container-fluid" style="background-color:#F44336;color:#fff;height:120px;">
  <h1>Rate Your Favourite</h1>
  <h3>Rate and Share your favourite songs here. See videos, download mp3 and many more...</h3> 
</div>

<nav class="navbar navbar-inverse">
      <ul class="nav navbar-nav">
      
        <li class=""><a href="" class="glyphicon glyphicon-triangle-left" onclick="history.go(-1);">Back</button></a></li>
        <li class="active"><a href="{{url('/')}}"><span class="glyphicon glyphicon-home home"></span> Home</a></li>
           
       
    </ul>
</nav>


@yield('content')

</body>
</html>
