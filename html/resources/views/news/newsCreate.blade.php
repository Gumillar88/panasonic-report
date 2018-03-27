@include('layouts.header', ['title' => 'Create Article - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Article</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/news/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="Article title" value="{{ old('title') }}">
            </div>

            <div class="form-group">
                <label for="news_dealer">Delaer</label>
                {!! Form::select('dealer_ID[]', $dealers, old('dealer_ID'), ['class' => 'form-control chosen-select', 'data-placeholder' => 'Select Dealer','multiple']) !!}
            </div>

            <div class="form-group">
                <label for="news_content">Content</label>
                <textarea class="form-control" name="content" id="news_content" placeholder="Article content" rows="4">{{ old('content') }}</textarea>
            </div>
            
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/news" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
