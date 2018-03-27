@include('layouts.header', ['title' => 'Remove Article - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Article</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/news/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the article ?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/news" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
