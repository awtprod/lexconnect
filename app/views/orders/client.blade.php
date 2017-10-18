@extends('layouts.default')
@section('head')


	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
	<script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
<style>

.smarty-autocomplete{
	display: none;
}
</style>

@stop
@section('content')
<h1>Create New Order</h1>


@if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

{{ Form::open(array('route' => 'orders.store','files'=>true, 'id'=> 'create')) }}


		{{ Form::hidden('company', $company) }}


	{{ Form::label('reference', 'Reference: ') }}
	{{ Form::text('reference') }}<br>


	{{ Form::label('requester', 'Requester: ') }}
	{{ Form::select('requester', $users, Auth::user()->id, ['id' => 'FullName']) }}<p>


    	<div id="service_options">
	<h2>Services:</h2>
	<input type="checkbox" name="service" class="services" value="service">Process Service &nbsp;
	<input type="checkbox" name="filing" class="services" value="filing">Filing/Recording &nbsp;
	<input type="checkbox" name="skip" class="services" value="skip">Skiptrace &nbsp;
	<input type="checkbox" name="court_run" class="services" value="court_run">Court Run/Miscellaneous

</div>

	<div id="filing" style="display:none;">

	<h2>Filing/Recording:</h2></p>

	{{ Form::label('filing', 'Filing: ') }}
	{{ Form::select('filing', array(''=>'Select Priority Level','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<br>

	{{ Form::label('recording', 'Recording: ') }}
	{{ Form::select('recording', array(''=>'Select Priority Level','Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<br>
	</div>


	<div id="court_run" style="display:none;">
		<h2>Document Retrieval</h2></p>
		Request Details:<br>
		<textarea name="run_notes" id="run_notes" rows="4" cols="50"></textarea><p>

		Support Documents: <input type="file" name="run_docs[0][file]" class="run_docs"><input type="hidden" name="run_docs[0][type]" value="run support docs" class="run_docs">&nbsp;

	<div class="add_court_run"></div>

	<button class="add_court_run_button">Add More Documents</button><br>
	</div>

	<div id="skip" hidden>
		<h2>Skip Trace</h2></p>
		Defendant: <input type="text" name="skip_defendant[1]" id="skip_defendant[1]">

		<div class="skip_defendants"></div>

		<button class="add_skip_button">Add More Servees</button><p>

		Request Details:<br>
		<textarea name="skip_notes" id="skip_notes" rows="4" cols="50"></textarea><p>

		Supporting Documents (obituary, etc):<br>
		<input type="file" name="skip_docs[0][file]" class="skip_docs"><input type="hidden" name="skip_docs[0][type]" value="skip trace" class="skip_docs">&nbsp;

		<div class="add_skip_docs"></div>

		<button class="add_skip_docs_button">Add More Documents</button><br>
	</div>
	<div class="process_service" hidden>
   <h2>Process Service: </h2><br>
		<div id="service-type">
			{{ Form::label('type', 'Service Type: ') }}
			{{ Form::label('type', 'Process Service') }}
			<input type="radio" name="type" id="service" value="service" checked>
			{{ Form::label('type', 'Property Posting') }}
			<input type="radio" name="type" id="post" value="posting">
			{{ Form::label('priority', 'Priority: ') }}
			{{ Form::select('priority', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
		</div>

	<div class="service_documents">


<h2>Service Documents:</h2>
		<input type="file" name="documents[0][file]" class="documents"><input type="hidden" name="documents[0][type]" value="service_documents" class="documents">&nbsp;

		<div class="document_wrapper"></div>

		<button class="add_document_button">Add More Documents</button>

		</div>

<p>

		<h2>Defendants:</h2>
		<input type="text" name="defendant[1]" id="defendant[1]"/>
		<div class="input_fields_wrap">


		</div><br>
<button class="add_defendant_form"> Add Defendants</button><p>



		Service Instructions:<br>
		<textarea name="service_notes" id="service_notes" rows="4" cols="50"></textarea><br>
</div><p>
		<div><button id="Submit" class="Submit">Submit</button>{{ Form::reset('Reset') }}</div>
{{ Form::close() }}



<a href="{{ URL::previous() }}">Go Back</a>

{{HTML::script('/js/client.js')}}

@stop
