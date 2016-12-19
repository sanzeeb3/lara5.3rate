
<div class="container-fluid" style="background-color:#F44336;color:#fff;height:140px;">
  <h1>Rate Your Favourite</h1>
  <h3>Rate and Share your favourite songs here. See videos, download mp3 and many more...<br>No login !! NO Registration!!</h3> 
</div>

<nav class="navbar navbar-inverse">
      <ul class="nav navbar-nav">
      
        <li class=""><a href="" class="glyphicon glyphicon-triangle-left" onclick="history.go(-1);">Back</button></a></li>
        <li class="active"><a href="{{url('/')}}"><span class="glyphicon glyphicon-home home"></span> Home</a></li>
           
       
    </ul>
</nav>

@extends('main')
@section('content')    
<div class="container">
	<div class="row">
		<div class="col-sm-3">
        	
            <table class="table table-bordered">
   				<label>Most Viewed Bands:</label>
   				<thead>
     				<tr><td class="bg-primary">Band</td><td class="bg-primary">Views</td></tr>
   				</thead>
   				<tbody>
     				@foreach($bands as $band)
     				<tr>	
     					<td class="bg-info">
							<div class="search"><a href="" data-id="<?php echo $band->id;?>">{{$band->name}}</a></div>
     					</td>
     				
     					<td class="bg-info">
							<b>{{$band->views}}</b>
     					</td>
     				</tr>	
     				@endforeach    
   				</tbody>
   			</table>

            @if(!Auth::check())
                <a href="{{route('login')}}"><img height="10%" width="100%" src="{{asset('public/images/download.png')}}"></a>     
        
            @else (Auth::check())
                <div class="alert alert-info">Logged in as <?php echo Auth::user()->name;?></div>
                <img src="<?php echo Auth::user()->avatar;?>">
                <a href="{{route('logout')}}"><button class="btn btn-info">Logout</button></a>
            @endif
        
    
            <h2>Add new song:</h2>
            <p style="color:red"><i>Note*: You must login to add new song.</i></p>
        
            <form id="add" method="POST" action="{{url('/rate/add')}}" enctype="multipart/form-data">
            {!! csrf_field() !!}
                <label>Song: <b style="color:red">*</b></label>
                <input type="text" name="song" class="form-control"><br>
                <label>Band: </b></label>
                <select class="form-control" name="selectBand">
                       <option value="6">--Select a band--</option>
                    @foreach($bands as $band)
                        <option value="<?php echo $band->id;?>">{{$band->name}}</option>
                    @endforeach
                </select><br>
                or Add new band: <input type="text" name="band" placeholder="Add only if not listed above" class="form-control"><br>
                
                <label>Upload Mp3:</label>
                
                <input id="input-id" type="file" accept=".mp3" name="file" class="form-control"><br>
                <input type="hidden" id="getfilename" name="uploadedfile" value="">   
                <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">

                <label>YouTube link:</label>
                <input type="text" name="link" class="form-control"><br>
                <input type="submit" class="btn btn-primary" value="Add">
            </form>

		</div>
	
		
        <div class="col-sm-6">
            <label>Most Voted Video:</label>
            <iframe width="560" height="315" src="<?php echo str_replace('watch?v=', 'embed/', $songs[0]->link);?>" frameborder="0" allowfullscreen></iframe>

            <br><br>
			<h2><label>All Songs:</label></h2>
			<table id="featured" class="table table-bordered">
   			<thead>
				<tr><td>S.N.</td><td>Song</td><td>Band</td><td>Total Votes</td><td>Options</td></tr>
			</thead>
			<tbody>
			
			<?php $i=1;?>
			@foreach($songs as $song)
				<tr>
					<td>
					<?php echo $i++;?>
					</td>
					<td>
						<b>{{$song->name}}</b><br>
	
     					<?php if($song->mp3)
                        {
                            ?><a href="{{asset("newuploads/{$song->mp3}")}}">Download mp3</a><br><?php
                        }
                        ?>
                        <?php if($song->link)
                        {
						    ?><a href="<?php echo $song->link;?>">See video (yoututbe)</a><?php
                        }
                        ?>
					</td>
					<td>
						{{$song->band->name}}
					</td>
					<td>
						<h3><b>{{$song->views}}</b></h3>
					</td>
					<td>
                    Add Votes.
                        <form action="{{url('/rate/rate')}}" id="rate" method="POST">
                        {!!csrf_field()!!}
						<select name="rate" class="form-control">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
                        <input type="hidden" name="id" value="<?php echo $song->id;?>">
					
                        <input type="submit" class="btn btn-info form-control" value="Vote"> 
                        </form>

					</td>
				</tr>
			@endforeach
			
			</tbody>
			</table>

		</div>

		<div class="col-sm-3">
            <div id="fb-root"></div>
            <div class="fb-page" data-href="https://www.facebook.com/Rock-Music-Fans-626992887455709/" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/Rock-Music-Fans-626992887455709/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/Rock-Music-Fans-626992887455709/">Rock Music Fans</a></blockquote></div><br><BR><BR>
    
        </div> 
        
    </div>
</div>

<script>

var table1=$('#featured').DataTable();

$(document).on('click','.search',function(e)
{
        e.preventDefault();
        var search = $(this).text();
       table1.search(search).draw();
      
});   
   
$("#add").validate({

    rules: {
        song: {
            required:true,

            },
         band:{
                    remote:
                      {
                            url: "{{url('/rate/checkBand')}}",
                            type: "post",
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                      },
                  },
           file: { 
                required: false,
                extension: "mp3|mp3g|mp4"
        }, 
    },

     messages:
        {
            song:{required:'Song is required.'},
            band:{remote:'Band already in use. Select from the dropdown list.'},
        },

    });

$(document).on('submit', '#add', function (e) 
{         
    e.preventDefault();
    var frm = $(this);
    $.ajax({
        type: frm.attr('method'),
        url: frm.attr('action'),
        data: frm.serialize(),
        success: function (data)
            {
                var res = $.parseJSON(data);
                if(res == true)
                {
                    swal({
                        title: "Your song has been added.",
                        type: "success",
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "OK!",
                        closeOnConfirm: false
                    },
                    	function(){
  						  window.location.href = "{{url('/')}}";
					});
                }

                else if(res == "notloggedin")
                {
                    alert('Login First!')
                }
                else

                {
                    swal("Opps!", "Something went wrong!. Try again", "error");
                }
                
            }
    });       
});  

$("#input-id").fileinput({
        maxFileSize: 264000,
        uploadUrl: "{{url('/rate/uploadfile')}}", 
        uploadAsync: true,
        uploadExtraData:{'_token':$("#csrf_token").val()},
        allowedFileExtensions: ['mp3', 'mp4', 'mpeg', 'flv'],
        maxFileCount: 1,
        showUpload: true,
        dropZoneEnabled: false,
      
});

$("#input-id").on('fileuploaderror', function(event, data, previewId, index) {
    var form = data.form, files = data.files, extra = data.extra,
        response = data.response, reader = data.reader;
    console.log(data.response.upload_error);
});

$("#input-id").on('fileuploaded', function(event, data, previewId, index) {
    var form = data.form, files = data.files, extra = data.extra,
        response = data.response, reader = data.reader;
    console.log(response);
    $('#getfilename').val(response);
});

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=690925494396768";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

</script>

@endsection
