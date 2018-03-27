@include('layouts.header', ['title' => 'Remove Dealer - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Dealer</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the dealer?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/dealer" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
