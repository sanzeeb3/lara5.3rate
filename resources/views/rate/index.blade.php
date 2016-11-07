@extends('main')
@section('content')    
<div class="container">
	<div class="row">
		<div class="col-sm-2">
        	<table class="table table-bordered">
   				<label>Most Viewed Bands:</label>
   				<thead>
     				<tr><td class="bg-primary">Band</td><td class="bg-primary">Views</td></tr>
   				</thead>
   				<tbody>
     				@foreach($bands as $band)
     				<tr>	
     					<td class="bg-info">
							{{$band->name}}
     					</td>
     				
     					<td class="bg-info">
							<b>{{$band->views}}</b>
     					</td>
     				</tr>	
     				@endforeach    
   				</tbody>
   			</table>
		</div>
	
		<div class="col-sm-6">
			<label>All Songs:</label>
			<table id="featured" class="table table-bordered">
   			<thead>
				<tr><td>S.N.</td><td>Song</td><td>Band</td><td>Rating</td><td>Options</td></tr>
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
						<a href="">Download mp3</a><br>
						<a href="<?php echo $song->link;?>">See video (yoututbe)</a>
					</td>
					<td>
						{{$song->band->name}}
					</td>
					<td>
						<h3><b>{{$song->views}}</b></h3>
					</td>
					<td>
						<select name="rate" class="form-control">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
						<input type="submit" class="form-control" value="Rate"> 
					</td>
				</tr>
			@endforeach
			
			</tbody>
			</table>

		</div>
		<div class="col-sm-4">
		<h2>Add new song:</h2>
			<form id="add" method="POST" action="{{url('/rate/add')}}" enctype="multipart/form-data">
		    {!! csrf_field() !!}
			    <label>Song: <b style="color:red">*</b></label>
				<input type="text" name="song" class="form-control"><br>
				<label>Band: </b></label>
                <select class="form-control" name="selectBand">
                    @foreach($bands as $band)
                        <option value="{{$band->id}}">{{$band->name}}</option>
                    @endforeach
                </select>
				<label>Upload Mp3:</label>
				
				<input id="input-id" type="file" accept=".mp3" name="file" class="form-control"><br>
				<input type="hidden" id="getfilename" name="uploadedfile" value="">   
				<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">

				<label>YouTube link:</label>
				<input type="text" name="link" class="form-control"><br>
				<input type="submit" class="btn btn-default" value="Add">
			</form>
	</div>
</div>


<script>

$('#featured').DataTable();
   
$("#add").validate({

    rules: {
        song: {
            required:true,

            },
     
           file: { 
                required: false,
                extension: "mp3|mp3g|mp4"
        }, 
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
    $('#getfileename').val(response);
});

</script>

@endsection