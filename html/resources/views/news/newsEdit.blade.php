@include('layouts.header', ['title' => 'Edit Article - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Article</h1>
    
     @if(Session::has('news-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/news/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $news->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="Article Title" value="{{ $news->title }}">
            </div>
            
            <div class="form-group">
                <label for="news_content">Content</label>
                <textarea class="form-control" name="content" id="news_content" placeholder="Article content" rows="4">{{ $news->content }}</textarea>
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/news" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
