@if(count($errors) > 0)
<div class="mt-4 flex flex-col flex-nowrap gap-3">
	@foreach($errors->all() as $error)
	<p class="mx-3 px-3 py-2 block bg-errorfondo text-errortexto text-ui text-center text-xl rounded-xl">{{$error}}</p>
	@endforeach
</div>
@endif