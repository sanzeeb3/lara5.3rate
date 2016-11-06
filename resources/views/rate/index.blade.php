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
							{{$band->views}}
     					</td>
     				</tr>	
     				@endforeach    
   				</tbody>
   			</table>
		</div>
	
		<div class="col-sm-6">
			<label>Featured Songs:</label>
			<table class="table table-bordered">
   			<thead>
				<tr><td>S.N.</td><td>Song</td><td>Band</td><td>Total votes</td><td>Options</td></tr>
			</thead>
			<tbody>
			<tr>
			<?php $i=1;?>
			@foreach($songs as $song)
				<tr>
					<td>
					<?php echo $i++;?>
					</td>
					<td>
						{{$song->name}}
					</td>
					<td>
						{{$song->band->name}}
					</td>
					<td>
						{{$song->views}}
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
			<form>
				<label>Song:</label>
				<input type="text" name="song" class="form-control"><br>
				<label>Band:</label>
				<input type="text" name="band" class="form-control"><br>
				<input type="submit" class="btn btn-default" value="Add">
			</form>
	</div>
</div>
@endsection